<?php

namespace App\Services\Website\Company;


use Carbon\Carbon;

class CompanyPromoteJobService
{
    /**
     * Promote job
     */
    public function execute($request, $jobCreated): mixed
    {
        $userplan = currentCompany()->userplan ?? abort(403);

        if (! auth('user')->check() || authUser()->role != 'company' || ! $userplan) {
            return abort(403);
        }

        $setting = loadSetting();
        
// condition to check the plan expiry            
$expiryDate = Carbon::parse($userplan->plan_expired_at);
// Compare the expiry date with the current date and time
if ($expiryDate < Carbon::now()) {
    // session()->flash('error', __('Your plan has been expired'));
    flashError(__('Your plan has been expired'));
    return redirect()->route('website.plan');
}else{
    

        if ($request->badge == 'featured') {
            if ($userplan->featured_job_limit) {
                $userplan->featured_job_limit = $userplan->featured_job_limit - 1;
                $userplan->save();
            } else {
                flashError(__('you_have_no_featured_job_limit'));

                return redirect()->route('website.plan');
            }

            $featured_days = $setting->featured_job_days > 0 ? now()->addDays($setting->featured_job_days)->format('Y-m-d') : null;

            $jobCreated->update([
                'featured' => 1,
                'highlight' => 0,
                'featured_until' => $featured_days,
                'highlight_until' => null,
            ]);
        } else {
            if ($userplan->highlight_job_limit) {
                $userplan->highlight_job_limit = $userplan->highlight_job_limit - 1;
                $userplan->save();
            } else {
                flashError(__('you_have_no_highlight_job_limit'));

                return redirect()->route('website.plan');
            }

            $highlight_days = $setting->highlight_job_days > 0 ? now()->addDays($setting->highlight_job_days)->format('Y-m-d') : null;

            $jobCreated->update([
                'featured' => 0,
                'highlight' => 1,
                'highlight_until' => $highlight_days,
                'featured_until' => null,
            ]);
        }


}
        return $jobCreated;
    }
}
