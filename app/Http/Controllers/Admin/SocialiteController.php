<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only(['update', 'updateStatus']);

        $this->middleware(['permission:setting.view|setting.update'])->only(['index']);

        $this->middleware(['permission:setting.update'])->only(['update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.settings.pages.socialite');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            switch ($request->type) {
                case 'google':
                    $this->updateGoogleCredential($request);
                    break;
                case 'facebook':
                    $this->updateFacebookCredential($request);
                    break;
                case 'twitter':
                    $this->updateTwitterkCredential($request);
                    break;
                case 'linkedin':
                    $this->updateLinkedinkCredential($request);
                    break;
                case 'github':
                    $this->updateGithubCredential($request);
                    break;
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update login with google credential
     *
     * @return void
     */
    public function updateGoogleCredential(Request $request)
    {
        try {
            $request->validate([
                'google_client_id' => ['required'],
                'google_client_secret' => ['required'],
            ]);

            $this->updateEnv($request);
            setEnv('GOOGLE_LOGIN_ACTIVE', $request->google ? 'true' : 'false');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update login with facebook credential
     *
     * @return void
     */
    public function updateFacebookCredential(Request $request)
    {
        try {
            $request->validate([
                'facebook_client_id' => ['required'],
                'facebook_client_secret' => ['required'],
            ]);

            $this->updateEnv($request);
            setEnv('FACEBOOK_LOGIN_ACTIVE', $request->facebook ? 'true' : 'false');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update login with twitter credential
     *
     * @return void
     */
    public function updateTwitterkCredential(Request $request)
    {
        try {
            $request->validate([
                'twitter_client_id' => ['required'],
                'twitter_client_secret' => ['required'],
            ]);

            $this->updateEnv($request);
            setEnv('TWITTER_LOGIN_ACTIVE', $request->twitter ? 'true' : 'false');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update login with linkedin credential
     *
     * @return void
     */
    public function updateLinkedinkCredential(Request $request)
    {
        try {
            $request->validate([
                'linkedin_client_id' => ['required'],
                'linkedin_client_secret' => ['required'],
            ]);

            $this->updateEnv($request);
            setEnv('LINKEDIN_LOGIN_ACTIVE', $request->linkedin ? 'true' : 'false');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update login with github credential
     *
     * @return void
     */
    public function updateGithubCredential(Request $request)
    {
        try {
            $request->validate([
                'github_client_id' => ['required'],
                'github_client_secret' => ['required'],
            ]);

            $this->updateEnv($request);
            setEnv('GITHUB_LOGIN_ACTIVE', $request->github ? 'true' : 'false');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update Socialite login credential in .env file
     *
     * @return void
     */
    protected function updateEnv(Request $request)
    {
        try {
            $data = $request->only(['google_client_id', 'google_client_secret', 'facebook_client_id', 'facebook_client_secret', 'twitter_client_id', 'twitter_client_secret', 'linkedin_client_id', 'linkedin_client_secret', 'github_client_id', 'github_client_secret']);

            foreach ($data as $key => $value) {
                if (env(strtoupper($key)) != $value) {
                    setEnv(strtoupper($key), $value);
                }
            }

            session()->flash('success', ucfirst($request->type).__('setting_updated_successfully'));

            return redirect()
                ->route('settings.social.login')
                ->send();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
