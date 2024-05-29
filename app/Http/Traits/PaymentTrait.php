<?php

namespace App\Http\Traits;

use App\Models\Admin;
use App\Models\Earning;
use App\Models\UserPlan;
use App\Notifications\Admin\NewPlanPurchaseNotification;
use App\Services\Website\Job\PayPerJobService;
use Illuminate\Support\Facades\Notification;

use Carbon\Carbon;
use App\Models\EarningResdex;
use App\Models\UserPlanResdex;
use App\Notifications\Admin\NewPlanResdexPurchaseNotification;

trait PaymentTrait
{
    use JobAble;

    public function orderPlacing($redirect = true)
    {
        $plan = session('plan');
        // print_r($plan);exit();
        $order_amount = session('order_payment');
        $transaction_id = session('transaction_id') ?? uniqid('tr_');
        $job_payment_type = session('job_payment_type') ?? 'package_job';

        info($order_amount);
        info($job_payment_type);

        $order = Earning::create([
            'order_id' => rand(1000, 999999999),
            'transaction_id' => $transaction_id,
            'plan_id' => $plan->id ?? null,
            'company_id' => currentCompany()->id,
            'payment_provider' => $order_amount['payment_provider'],
            'amount' => $order_amount['amount'],
            'currency_symbol' => $order_amount['currency_symbol'],
            'usd_amount' => $order_amount['usd_amount'],
            'payment_status' => 'paid',
            'payment_type' => $job_payment_type == 'per_job' ? 'per_job_based' : 'subscription_based',
        ]);

        info($order);

        if ($job_payment_type == 'package_job') {
            info('condition true');

            $user_plan = UserPlan::companyData()->first();
            $company = currentCompany();

// to add/update the expiry column
$currentDate = Carbon::now();
// $newDate = $currentDate->addDays(15);
$newDate = $currentDate->addDays($plan->plan_valid_days);
$result = $newDate->format('Y-m-d H:i:s');


            if ($user_plan) {
                $user_plan->update([
                    'plan_id' => $plan->id,
                    'job_limit' => $user_plan->job_limit + $plan->job_limit,
                    'featured_job_limit' => $user_plan->featured_job_limit + $plan->featured_job_limit,
                    'highlight_job_limit' => $user_plan->highlight_job_limit + $plan->highlight_job_limit,
                    'candidate_cv_view_limit' => $user_plan->candidate_cv_view_limit + $plan->candidate_cv_view_limit,
                    'candidate_cv_view_limitation' => $plan->candidate_cv_view_limitation,
                    'plan_expired_at' => $result,
                ]);
            } else {
                $company->userPlan()->create([
                    'plan_id' => $plan->id,
                    'job_limit' => $plan->job_limit,
                    'featured_job_limit' => $plan->featured_job_limit,
                    'highlight_job_limit' => $plan->highlight_job_limit,
                    'candidate_cv_view_limit' => $plan->candidate_cv_view_limit,
                    'candidate_cv_view_limitation' => $plan->candidate_cv_view_limitation,
                    'plan_expired_at' => $result,
                ]);
            }

            if (checkMailConfig()) {
                // make notification to admins for approved
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    Notification::send($admin, new NewPlanPurchaseNotification($admin, $order, $plan, authUser()));
                }
            }

            storePlanInformation();

            info('every thing is ok');

        } else {
            info('condition false');

            return $this->storeJobData();
        }

        $this->forgetSessions();

        if ($redirect) {
            session()->flash('success', __('plan_purchased_successfully'));

            return redirect()->route('company.plan')->send();
        }

        info('redirecting to success');

        return true;
    }

    private function forgetSessions()
    {
        session()->forget('plan');
        session()->forget('order_payment');
        session()->forget('transaction_id');
        session()->forget('stripe_amount');
        session()->forget('razor_amount');
        session()->forget('job_payment_type');
    }

    private function storeJobData()
    {
        $jobCreated = (new PayPerJobService())->execute();

        $this->forgetSessions();

        $message = $jobCreated->status == 'active' ? __('job_has_been_created_and_published') : __('job_has_been_created_and_waiting_for_admin_approval');

        session()->flash('success', $message);

        return redirect()->route('website.job.details', $jobCreated->slug)->send();
    }
    
    
    
// for resdex plans
    public function orderPlacingResdex($redirect = true)
    {
        $plan = session('plan');
        // print_r($plan);exit();
        $order_amount = session('order_payment');
        $transaction_id = session('transaction_id') ?? uniqid('tr_');
        $job_payment_type = session('job_payment_type') ?? 'package_job';

        info($order_amount);
        info($job_payment_type);

        $order = EarningResdex::create([
            'order_id' => rand(1000, 999999999),
            'transaction_id' => $transaction_id,
            'plan_id' => $plan->id ?? null,
            'company_id' => currentCompany()->id,
            'payment_provider' => $order_amount['payment_provider'],
            'amount' => $order_amount['amount'],
            'currency_symbol' => $order_amount['currency_symbol'],
            'usd_amount' => $order_amount['usd_amount'],
            'payment_status' => 'paid',
            'payment_type' => $job_payment_type == 'per_job' ? 'per_job_based' : 'subscription_based',
        ]);

        info($order);

        if ($job_payment_type == 'package_job') {
            info('condition true');

            $user_plan = UserPlanResdex::companyData()->first();
        // print_r($user_plan);exit();
            $company = currentCompany();

// to add/update the expiry column
$currentDate = Carbon::now();
// $newDate = $currentDate->addDays(15);
$newDate = $currentDate->addDays($plan->plan_valid_days);
$result = $newDate->format('Y-m-d H:i:s');


            if ($user_plan) {
                $user_plan->update([
                    'resdex_plan_id' => $plan->id,
                    'credit_limit' => $user_plan->credit_limit + $plan->credits,
                    'plan_expired_at' => $result,
                ]);
            } else {
                $company->userPlanResdex()->create([
                    'resdex_plan_id' => $plan->id,
                    'credit_limit' => $plan->credits,
                    'plan_expired_at' => $result,
                ]);
            }

            if (checkMailConfig()) {
                // make notification to admins for approved
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    Notification::send($admin, new NewPlanResdexPurchaseNotification($admin, $order, $plan, authUser()));
                }
            }

            storePlanInformation();

            info('every thing is ok');

        } 
        // else {
        //     info('condition false');

        //     return $this->storeJobDataResdex();
        // }

        $this->forgetSessionsResdex();

        if ($redirect) {
            session()->flash('success', __('plan_purchased_successfully'));

            return redirect()->route('company.plan')->send();
        }

        info('redirecting to success');

        return true;
    }

    private function forgetSessionsResdex()
    {
        session()->forget('plan');
        session()->forget('order_payment');
        session()->forget('transaction_id');
        session()->forget('stripe_amount');
        session()->forget('razor_amount');
        session()->forget('job_payment_type');
    }

    // private function storeJobDataResdex()
    // {
    //     $jobCreated = (new PayPerJobService())->execute();

    //     $this->forgetSessions();

    //     $message = $jobCreated->status == 'active' ? __('job_has_been_created_and_published') : __('job_has_been_created_and_waiting_for_admin_approval');

    //     session()->flash('success', $message);

    //     return redirect()->route('website.job.details', $jobCreated->slug)->send();
    // }
}
