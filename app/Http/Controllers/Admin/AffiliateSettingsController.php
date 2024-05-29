<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AffiliateSettingsController extends Controller
{
    /*
    * Show the affiliate settings page
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function index()
    {
        return view('backend.settings.pages.affiliate');
    }

    /*
    * Update the careerjet settings
    *
    * @param Request $request
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function careerjetUpdate(Request $request)
    {
        $request->validate([
            'default_locale' => 'required',
            'job_limit' => 'required',
        ]);

        setEnv('CARRERJET_ID', $request->careerjet_affiliate_id ?? '');
        checkSetEnv('CARRERJET_LIMIT', $request->job_limit);
        checkSetEnv('CARRERJET_DEFAULT_LOCALE', $request->default_locale);

        flashSuccess(__('careerjet_api_configuration_updated'));

        return back();
    }

    /*
    * Update the indeed affiliate settings
    *
    * @param Request $request
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function indeedUpdate(Request $request)
    {
        $request->validate([
            'job_limit' => 'required',
        ]);

        setEnv('INDEED_ID', $request->indeed_affiliate_id ?? '');
        checkSetEnv('INDEED_LIMIT', $request->job_limit);

        flashSuccess(__('indeed_api_configuration_updated'));

        return back();
    }

    /*
    * Set the default job provider
    *
    * @param Request $request
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function setDefaultJob(Request $request)
    {
        $provider = $request->job_provider;

        if ($provider) {
            setEnv('DEFAULT_JOB_PROVIDER', $provider);
        } else {
            envReplace('DEFAULT_JOB_PROVIDER', '');
        }

        flashSuccess(__('default_affiliate_job_provider_updated'));

        return back();
    }
}
