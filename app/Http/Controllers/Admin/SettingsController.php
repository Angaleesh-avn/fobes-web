<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminGeneralSettingUpdateRequest;
use App\Http\Requests\AdminMailUpdateRequest;
use App\Http\Requests\AdminPayperjobSettingUpdateRequest;
use App\Http\Requests\AdminWPUpdateRequest;
use App\Http\Traits\UploadAble;
use App\Mail\SmtpTestEmail;
use App\Models\cms;
use App\Models\Cookies;
use App\Models\Job;
use App\Models\LanguageData;
use App\Models\Setting;
use App\Models\Timezone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Modules\Currency\Entities\Currency;
use Modules\Currency\Http\Controllers\CurrencyController;
use Modules\Language\Entities\Language;
use Modules\Language\Http\Controllers\TranslationController;
use Modules\Location\Entities\Country;
use Modules\Seo\Entities\Seo;
use Modules\Seo\Entities\SeoPageContent;
use Modules\SetupGuide\Entities\SetupGuide;
use msztorc\LaravelEnv\Env;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SettingsController extends Controller
{
    use UploadAble;

    public $setting;

    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'generalUpdate',
            'custumCSSJSUpdate',
            'emailUpdate',
            'systemUpdate',
            'cookiesUpdate',
            'seoUpdate',
            'recaptchaUpdate',
            'colorUpdate',
            'layoutUpdate',
            'pwaUpdate',
        ]);
        $this->middleware('access_limitation')->only(['generalUpdate', 'custumCSSJSUpdate', 'emailUpdate', 'systemUpdate', 'cookiesUpdate', 'seoUpdate', 'recaptchaUpdate', 'colorUpdate', 'layoutUpdate', 'pwaUpdate']);

        $this->setting = loadSetting(); // see helpers.php
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function general()
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            $data['timezones'] = Timezone::all();
            $data['currencies'] = Currency::all();
            $data['countries'] = Country::all();
            $data['setting'] = $this->setting;

            return view('backend.settings.pages.general', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function theme()
    {
        abort_if(! userCan('setting.view'), 403);

        return view('backend.settings.pages.theme');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function custom()
    {
        abort_if(! userCan('setting.view'), 403);

        return view('backend.settings.pages.custom');
    }

    /**
     * Website Data Update.
     *
     * @return void
     */
    public function generalUpdate(AdminGeneralSettingUpdateRequest $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            if ($request->name && $request->name != config('app.name')) {
                setEnv('APP_NAME', $request->name);
            }

            $setting = $this->setting;

            if ($request->hasFile('dark_logo')) {
                $setting['dark_logo'] = uploadFileToPublic($request->dark_logo, 'app/logo');
                deleteFile($setting->dark_logo);
            }

            if ($request->hasFile('light_logo')) {
                $setting['light_logo'] = uploadFileToPublic($request->light_logo, 'app/logo');
                deleteFile($setting->light_logo);
            }

            if ($request->hasFile('favicon_image')) {
                $setting['favicon_image'] = uploadFileToPublic($request->favicon_image, 'app/logo');
                deleteFile($setting->favicon_image);
            }

            $setting->email = $request->email;

            $setting->save();
            SetupGuide::where('task_name', 'app_setting')->update(['status' => 1]);

            return back()->with('success', 'Website setting updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function preferenceUpdate(Request $request)
    {
        try {
            // validation
            $request->validate([
                'footer_phone_no' => ['nullable'],
                'footer_facebook_link' => ['nullable', 'url'],
                'footer_instagram_link' => ['nullable', 'url'],
                'footer_twitter_link' => ['nullable', 'url'],
                'footer_youtube_link' => ['nullable', 'url'],
                'footer_linkedIn_link' => ['nullable', 'url'],
            ]);

            //Footer Update
            $cms = cms::first();
            $cms->footer_phone_no = $request->footer_phone_no;
            $cms->footer_facebook_link = $request->footer_facebook_link;
            $cms->footer_instagram_link = $request->footer_instagram_link;
            $cms->footer_twitter_link = $request->footer_twitter_link;
            $cms->footer_youtube_link = $request->footer_youtube_link;
            $cms->footer_linkedIn_link = $request->footer_linkedIn_link;
            $cms->save();

            return back()->with('success', 'Website Footer Info updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update website layout
     *
     * @return void
     */
    public function layoutUpdate()
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $this->setting->update(request()->only('default_layout'));

            return back()->with('success', 'Website layout updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * color Data Update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function colorUpdate()
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $this->setting->update(request()->only(['sidebar_color', 'nav_color', 'sidebar_txt_color', 'nav_txt_color', 'main_color', 'accent_color', 'frontend_primary_color', 'frontend_secondary_color']));

            SetupGuide::where('task_name', 'theme_setting')->update(['status' => 1]);

            return back()->with('success', 'Color setting updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * custom js and css Data Update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function custumCSSJSUpdate()
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $this->setting->update(request()->only(['header_css', 'header_script', 'body_script']));

            return back()->with('success', 'Custom css/js updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Mode Update.
     *
     * @return bool
     */
    public function modeUpdate(Request $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $dark_mode = $request->only(['dark_mode']);
            $this->setting->update($dark_mode);

            return back()->with('success', 'Theme updated successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function email()
    {
        return view('backend.settings.pages.mail');
    }

    /**
     * Update mail configuration settings on .env file
     *
     * @return void
     */
    public function emailUpdate(AdminMailUpdateRequest $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $data = $request->only(['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_name', 'mail_from_address']);

            foreach ($data as $key => $value) {
                $env = new Env();
                $env->setValue(strtoupper($key), $value);
            }
            SetupGuide::where('task_name', 'smtp_setting')->update(['status' => 1]);

            return back()->with('success', 'Mail configuration update successfully');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Send a test email for check mail configuration credentials
     *
     * @return void
     */
    public function testEmailSent(Request $request)
    {
        $request->validate(['test_email' => ['required', 'email']]);
        try {
            Mail::to($request->test_email)->send(new SmtpTestEmail());

            return back()->with('success', 'Test email sent successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Mail send failed: '.$th->getMessage());
        }
    }

    /**
     * View Website mode page
     *
     * @return void
     */
    public function system()
    {
        try {
            // return 1;
            abort_if(! userCan('setting.view'), 403);

            $timezones = Timezone::all();
            $setting = $this->setting;
            $currencies = Currency::all();

            return view('backend.settings.pages.preference', compact('timezones', 'setting', 'currencies'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function systemUpdate(Request $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            // timezone update
            if ($request->has('timezone')) {
                $this->timezone($request);
            }

            // set default language
            if ($request->has('code')) {
                (new TranslationController())->setDefaultLanguage($request);
            }

            // app mode update
            if ($request->app_debug == 1) {
                Artisan::call('env:set APP_DEBUG=true');
            } else {
                Artisan::call('env:set APP_DEBUG=false');
            }

            // language changing update
            if ($request->has('language_changing')) {
                $this->allowLanguageChange($request);
            }

            // set default currency
            if ($request->has('currency')) {
                (new CurrencyController())->defaultCurrency($request);
            }

            // setting update
            $this->setting->update([
                'email_verification' => $request->email_verification ? true : false,
                'employer_auto_activation' => $request->employer_auto_activation ? true : false,
                'candidate_account_auto_activation' => $request->candidate_account_auto_activation ? true : false,
                'job_auto_approved' => $request->job_approval ? true : false,
                'edited_job_auto_approved' => $request->edited_job_approval ? true : false,
                'currency_switcher' => $request->currency_switcher,
            ]);

            return redirect()
                ->back()
                ->with('success', 'App configuration update successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function systemModeUpdate(Request $request)
    {
        try {
            if ($request->app_mode == 'live') {
                setEnv('APP_MODE', $request->app_mode);

                return back()->with('success', 'App is now live mode');
            } elseif ($request->app_mode == 'maintenance') {
                setEnv('APP_MODE', $request->app_mode);

                return back()->with('success', 'App is in maintenance mode!');
            } else {
                setEnv('APP_MODE', $request->app_mode);

                return back()->with('success', 'App is in coming soon mode!');
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update google analytics setting
     *
     * @return void
     */
    public function googleAnalytics($request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            if ($request->google_analytics == 1) {
                $this->setting->update(['google_analytics' => true]);
            } else {
                $this->setting->update(['google_analytics' => false]);
            }

            $env = new Env();
            $env->setValue(strtoupper('GOOGLE_ANALYTICS_ID'), request('google_analytics_id', ''));

            session()->put('google_analytics', request('google_analytics', 0));

            return back()->with('success', 'Google Analytics update successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update facebook pixel setting
     *
     * @return void
     */
    public function facebookPixel($request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $env = new Env();
            $env->setValue(strtoupper('FACEBOOK_PIXEL_ID'), request('facebook_pixel_id', ''));

            if ($request->facebook_pixel == 1) {
                $this->setting->update([
                    'facebook_pixel' => true,
                ]);
            } else {
                $this->setting->update([
                    'facebook_pixel' => false,
                ]);
            }

            session()->put('facebook_pixel', request('facebook_pixel', 0));

            return back()->with('success', 'Facebook Pixel update successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Allow language changing
     *
     * @return void
     */
    public function allowLanguageChange($request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $this->setting->update([
                'language_changing' => request('language_changing', 0),
            ]);

            flashSuccess(__('language_changing_status_changed'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update timezone
     *
     * @return void
     */
    public function timezone($request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $request->validate(['timezone' => 'required']);

            $timezone = $request->timezone;

            if ($timezone && $timezone != config('app.timezone')) {
                envReplace('APP_TIMEZONE', $timezone);

                flashSuccess(__('timezone_updated_successfully'));
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Cookies Settings fetch
     *
     * @return void
     */
    public function cookies()
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            $cookie = Cookies::firstOrFail();

            return view('backend.settings.pages.cookies', compact('cookie'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Cookies Settings update
     *
     * @return void
     */
    public function cookiesUpdate(Request $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            // validating request data
            $request->validate([
                'cookie_name' => 'required|max:50|string',
                'cookie_expiration' => 'required|numeric|max:365',
            ]);

            // updating data to database
            $cookies = Cookies::first();
            $cookies->allow_cookies = request('allow_cookies', 0);
            $cookies->cookie_name = $request->cookie_name;
            $cookies->cookie_expiration = $request->cookie_expiration;
            $cookies->force_consent = request('force_consent', 0);
            $cookies->darkmode = request('darkmode', 0);
            $cookies->save();

            flashSuccess(__('cookies_settings_successfully_updated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Seo Settings fetch
     *
     * @return void
     */
    public function seoIndex(Request $request)
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            $query = $request->lang_query;
            $seos = Seo::with([
                'contents' => function ($q) use ($query) {
                    if ($query) {
                        return $q->where('language_code', $query);
                    } else {
                        return $q->where('language_code', 'en');
                    }
                },
            ])->paginate(20);

            $languages = Language::get(['id', 'code', 'name']);

            return view('backend.settings.pages.seo.index', compact('seos', 'languages'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Seo Settings fetch
     *
     * @return void
     */
    public function seoEdit($page)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $seo = Seo::FindOrFail($page);
            $en_content = $seo
                ->contents()
                ->where('language_code', 'en')
                ->first();

            if (request('lang_query')) {
                $exist_content = $seo
                    ->contents()
                    ->where('language_code', request('lang_query'))
                    ->first();

                if (! $exist_content) {
                    $new_content = $seo->contents()->create([
                        'language_code' => request('lang_query'),
                        'title' => $en_content->title,
                        'description' => $en_content->description,
                        'image' => $en_content->image,
                    ]);
                }
            }

            if (request('lang_query')) {
                $content = $seo
                    ->contents()
                    ->where('language_code', request('lang_query'))
                    ->first();
            } else {
                $content = $seo->contents()->first();
            }

            $seo->load('contents');
            $languages = Language::get(['id', 'code', 'name']);

            return view('backend.settings.pages.seo.edit', compact('seo', 'languages', 'content'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Seo content create
     *
     * @return void
     */
    public function seoContentCreate(Request $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $seo = Seo::FindOrFail($request->page_id);
            $exist_content = $seo
                ->contents()
                ->where('language_code', $request->language_code)
                ->first();
            $en_content = $seo
                ->contents()
                ->where('language_code', 'en')
                ->first();

            $content = '';
            if ($exist_content) {
                $content = $exist_content;
            } else {
                $new_content = $seo->contents()->create([
                    'language_code' => $request->language_code,
                    'title' => $en_content->title,
                    'description' => $en_content->description,
                    'image' => $en_content->image,
                ]);
                $content = $new_content;
            }

            return redirect()->route('settings.seo.edit', [$seo->id, 'lang_query' => $content->language_code]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Seo content delete
     *
     * @return void
     */
    public function seoContentDelete(Request $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $content = SeoPageContent::FindOrFail($request->content_id);
            $content->delete();

            flashSuccess(__('success'), 'page_translation_content_delete_successfully');

            return redirect()->route('settings.seo.edit', [$request->page_id, 'lang_query' => 'en']);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Seo content update
     *
     * @return void
     */
    public function seoUpdate(Request $request, SeoPageContent $content)
    {
        try {
            abort_if(! userCan('setting.update'), 403);

            $request->validate(['title' => 'required', 'description' => 'required']);

            $content->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            if ($request->image != null && $request->hasFile('image')) {
                deleteFile($content->image);

                $path = 'images/seo';
                $image = uploadImage($request->image, $path);

                $content->update(['image' => $image]);
            }

            flashSuccess(__('page_meta_content_updated_successfully'));

            return redirect()->back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Working Process Settings update
     *
     * @param  SeoPageContent  $content
     * @return void
     */
    public function workingProcessUpdate(AdminWPUpdateRequest $request)
    {
        try {
            abort_if(! userCan('setting.update'), 403);
            session(['tab_part' => 'working_process']);

            $this->setting->update([
                'working_process_step1_title' => $request->working_process_step1_title,
                'working_process_step1_description' => $request->working_process_step1_description,
                'working_process_step2_title' => $request->working_process_step2_title,
                'working_process_step2_description' => $request->working_process_step2_description,
                'working_process_step3_title' => $request->working_process_step3_title,
                'working_process_step3_description' => $request->working_process_step3_description,
                'working_process_step4_title' => $request->working_process_step4_title,
                'working_process_step4_description' => $request->working_process_step4_description,
            ]);

            $workingProcess ? flashSuccess(__('work_process_content_updated')) : flashError();

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Generate sitemap
     *
     * @param  AdminWPUpdateRequest  $request
     * @param  SeoPageContent  $content
     * @return void
     */
    public function generateSitemap()
    {
        try {
            $jobs = Job::where('status', 'active')->get(['id', 'slug']);
            $employers = User::where('role', 'company')->get(['id', 'username']);

            $sitemap = Sitemap::create()
                ->add(Url::create('/home'))
                ->add(Url::create('/jobs'))
                ->add(Url::create('/candidates'))
                ->add(Url::create('/employers'))
                ->add(Url::create('/about'))
                ->add(Url::create('/contact'))
                ->add(Url::create('/login'))
                ->add(Url::create('/register'))
                ->add(Url::create('/faq'))
                ->add(Url::create('/plans'))
                ->add(Url::create('/posts'))
                ->add(Url::create('/posts'));

            foreach ($jobs as $job) {
                $jobUrl = '/job/'.$job->slug;
                $sitemap->add(Url::create($jobUrl));
            }

            foreach ($employers as $employer) {
                $employerUrl = '/employer/'.$employer->username;
                $sitemap->add(Url::create($employerUrl));
            }

            $sitemap->writeToFile(public_path('sitemap.xml'));
            flashSuccess('Sitemap regenerated');

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

        foreach ($employers as $employer) {
            $employerUrl = '/employer/' . $employer->username;
            $sitemap->add(Url::create($employerUrl));
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));
        flashSuccess('Sitemap regenerated');

        return back();
    }

    /**
     * Recaptcha Settings update
     *
     * @return void
     */
    public function recaptchaUpdate(Request $request)
    {
        $request->validate(['nocaptcha_key' => 'required', 'nocaptcha_secret' => 'required']);

        try {
            checkSetEnv('NOCAPTCHA_SITEKEY', $request->nocaptcha_key);
            checkSetEnv('NOCAPTCHA_SECRET', $request->nocaptcha_secret);
            setEnv('NOCAPTCHA_ACTIVE', $request->status ? 'true' : 'false');

            flashSuccess(__('recaptcha_configuration_updated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Google Analytics Settings update
     *
     * @return void
     */
    public function analyticsUpdate(Request $request)
    {
        try {
            $request->validate([
                'is_analytics_active' => 'nullable|boolean',
                'analytics_id' => 'required_if:is_analytics_active,1', // G-GTRVREE0F4
            ]);

            checkSetEnv('GOOGLE_ANALYTICS_ID', $request->analytics_id);
            setEnv('GOOGLE_ANALYTICS_STATUS', $request->is_analytics_active ? 'true' : 'false');

            flashSuccess('Google Analytics Configuration updated!');

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Payperjob update
     *
     *
     * @return void
     */
    public function payperjobUpdate(AdminPayperjobSettingUpdateRequest $request)
    {
        try {
            // payper setting data
            $this->setting->update([
                'per_job_active' => $request->status ?? 0,
                'per_job_price' => $request->per_job_price,
                'highlight_job_price' => $request->highlight_job_price,
                'featured_job_price' => $request->featured_job_price,
                'highlight_job_days' => $request->highlight_job_days,
                'featured_job_days' => $request->featured_job_days,
            ]);

            // forget session data
            cache()->forget('highlight_job_days');
            cache()->forget('featured_job_days');

            flashSuccess(__('payperjob_setting_updated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Application job deadline limit update
     *
     * @return Response
     */
    public function systemJobdeadlineUpdate(Request $request)
    {
        try {
            $this->setting->update([
                'job_deadline_expiration_limit' => $request->job_deadline_expiration_limit,
            ]);

            flashSuccess(__('job_deadline_expiration_limit_updated_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Upgrade application
     *
     * @return Response
     */
    public function upgrade()
    {
        return view('backend.settings.pages.upgrade-guide');
    }

    /**
     * Upgrade applying
     *
     * @return Response
     */
    public function upgradeApply()
    {
        try {
            Artisan::call('migrate');

            $this->syncLanguageJson();
            // menu list cache clear
            Cache::forget('menu_lists');

            flashSuccess(__('application_upgrade_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    private function syncLanguageJson()
    {
        try {
            $langData = LanguageData::get();

            foreach ($langData as $value) {
                $currentJsonPath = base_path('resources/lang/'.$value->code.'.json');
                $currentJson = json_decode(File::get($currentJsonPath), true);
                $databaseJson = json_decode($value->data, true);

                // Merge the current JSON with the database JSON, keeping existing keys
                $mergedJson = array_merge($currentJson, $databaseJson);

                // Save the merged JSON back to the database
                $value->update(['data' => json_encode($mergedJson)]);

                // Save the merged JSON back to the JSON file
                File::put($currentJsonPath, json_encode($mergedJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * PWA Data Update.
     *
     * @return void
     */
    public function pwaUpdate(Request $request)
    {
        abort_if(!userCan('setting.update'), 403);

        $request->validate([
            'app_pwa_icon' => ['nullable', 'mimes:png,jpg,jpeg'],
        ]);


        $setting = $this->setting;

        if ($request->hasFile('app_pwa_icon')) {
            $setting['app_pwa_icon'] = uploadFileToPublic($request->app_pwa_icon, 'app/logo');
            deleteFile($setting->app_pwa_icon);
        }

        // for pwa_enable
        $setting['pwa_enable'] = $request->pwa_enable;

        if ($request->pwa_enable) {
            updateManifest($setting);
        }

        $setting->save();
        SetupGuide::where('task_name', 'app_setting')->update(['status' => 1]);

        return back()->with('success', 'Website setting updated successfully!');
    }
}
