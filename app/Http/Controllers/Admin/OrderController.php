<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Earning;
use App\Models\ManualPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Plan\Entities\Plan;


use App\Models\EarningResdex;
use Modules\Plan\Entities\PlanResdex;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            abort_if(! userCan('order.view'), 403);

            $companies = Company::with('user')
                ->select('id', 'user_id')
                ->get()
                ->makeHidden(['fullteam_size', 'banner_url', 'logo_url']);
            $plans = Plan::all();

            $order_query = Earning::query();

            if (request()->has('company') && request('company') != null) {
                $order_query->where('company_id', request('company'));
            }

            if (request()->has('plan') && request('plan') != null) {
                $order_query->where('plan_id', request('plan'));
            }

            if (request()->has('provider') && request('provider') != null) {
                $order_query->where('payment_provider', request('provider'));
            }

            if (request()->has('sort_by') && request('sort_by') != null) {
                if (request('sort_by') == 'latest') {
                    $order_query->latest();
                } else {
                    $order_query->oldest();
                }
            } else {
                $order_query->latest();
            }

            $orders = $order_query->with(['company.user', 'plan', 'manualPayment:id,name'])->paginate(10)->withQueryString();
            $current_currency = currentCurrency();

            return view('backend.order.index', compact('orders', 'companies', 'plans', 'current_currency'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function create()
    {
        try {
            $companies = User::where('role', 'company')->get();
            $plans = Plan::get(['id', 'label']);
            $manuals_payments = ManualPayment::get();

            return view('backend.order.create', compact('companies', 'plans', 'manuals_payments'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function store(Request $request)
    {
        try {
            $plan = Plan::find($request->plan_id);

            $earning = new Earning();
            $earning->order_id = uniqid();
            $earning->transaction_id = uniqid('tr_');
            $earning->payment_provider = $request->payment_provider;
            $earning->plan_id = $request->plan_id;
            $earning->amount = $plan->price;
            $earning->company_id = $request->company_id;
            $earning->manual_payment_id = $request->payment_provider == 'offline' ? $request->manual_payment_id : null;
            $earning->payment_status = $request->status;
            $earning->payment_type = 'subscription_based';
            $earning->currency_symbol = env('APP_CURRENCY_SYMBOL');
            $earning->usd_amount = usdAmount($plan->price);
            $earning->save();

            return redirect()->route('order.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function show($id)
    {
        try {
            abort_if(! userCan('order.view'), 403);
            $order = Earning::with('plan', 'company', 'manualPayment:id,name')->findOrFail($id);

            return view('backend.order.show', compact('order'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
    
    
    
    
    public function index_resdex(Request $request)
    {
        try {
            abort_if(! userCan('order.view'), 403);

            $companies = Company::with('user')
                ->select('id', 'user_id')
                ->get()
                ->makeHidden(['fullteam_size', 'banner_url', 'logo_url']);
            $plans = PlanResdex::all();

            $order_query = EarningResdex::query();

            if (request()->has('company') && request('company') != null) {
                $order_query->where('company_id', request('company'));
            }

            if (request()->has('plan') && request('plan') != null) {
                $order_query->where('plan_id', request('plan'));
            }

            if (request()->has('provider') && request('provider') != null) {
                $order_query->where('payment_provider', request('provider'));
            }

            if (request()->has('sort_by') && request('sort_by') != null) {
                if (request('sort_by') == 'latest') {
                    $order_query->latest();
                } else {
                    $order_query->oldest();
                }
            } else {
                $order_query->latest();
            }

            $orders = $order_query->with(['company.user', 'plan', 'manualPayment:id,name'])->paginate(10)->withQueryString();
            $current_currency = currentCurrency();

            return view('backend.order.index_resdex', compact('orders', 'companies', 'plans', 'current_currency'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function create_resdex()
    {
        try {
            $companies = User::where('role', 'company')->get();
            $plans = PlanResdex::get(['id', 'label']);
            $manuals_payments = ManualPayment::get();

            return view('backend.order.create_resdex', compact('companies', 'plans', 'manuals_payments'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function store_resdex(Request $request)
    {
        try {
            $plan = PlanResdex::find($request->plan_id);

            $earning = new EarningResdex();
            $earning->order_id = uniqid();
            $earning->transaction_id = uniqid('tr_');
            $earning->payment_provider = $request->payment_provider;
            $earning->plan_id = $request->plan_id;
            $earning->amount = $plan->price;
            $earning->company_id = $request->company_id;
            $earning->manual_payment_id = $request->payment_provider == 'offline' ? $request->manual_payment_id : null;
            $earning->payment_status = $request->status;
            $earning->payment_type = 'subscription_based';
            $earning->currency_symbol = env('APP_CURRENCY_SYMBOL');
            $earning->usd_amount = usdAmount($plan->price);
            $earning->save();

            return redirect()->route('order.index_resdex');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function show_resdex($id)
    {
        try {
            abort_if(! userCan('order.view'), 403);
            $order = EarningResdex::with('plan', 'company', 'manualPayment:id,name')->findOrFail($id);
            // return $order;
            return view('backend.order.show_resdex', compact('order'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
