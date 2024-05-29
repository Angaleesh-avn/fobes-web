<?php

namespace App\Providers;

use App\Http\Traits\GetMenuTrait;
use App\Models\Cms;
use App\Models\Cookies;
use App\Models\WebsiteSetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Modules\Currency\Entities\Currency;
use Modules\Language\Entities\Language;
use Modules\Location\Entities\Country;
use Modules\SetupGuide\Entities\SetupGuide;

class AppServiceProvider extends ServiceProvider
{
    use GetMenuTrait;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        if (! app()->runningInConsole()) {
            $setting = loadSetting();

            $default_language = Language::where('code', config('templatecookie.default_language'))->first();
            view()->share('defaultLanguage', $default_language);

            $cookies = Cookies::first();
            view()->share('cookies', $cookies);

            view()->share('website_setting', WebsiteSetting::first());
            view()->share('cms_setting', Cms::first());

            // menu data
            view()->composer('*', function ($view) {
                $view->with('public_menu_lists', $this->publicMenu());
            });
            view()->composer('*', function ($view) {
                $view->with('company_menu_lists', $this->companyMenu());
            });
            view()->composer('*', function ($view) {
                $view->with('candidate_menu_lists', $this->candidateMenu());
            });

            view()->share('hcountries', Country::all());

            $appSetup = SetupGuide::orderBy('status', 'asc')->get();
            view()->share('appSetup', $appSetup);

            view()->share('setting', $setting);
            view()->share('currency_symbol', config('jobpilot.currency_symbol'));

            $languages = loadLanguage();
            $headerCountries = Country::select('id', 'name', 'slug', 'icon')->active()->get();
            $headerCurrencies = Currency::all();

            view()->share('languages', $languages);
            view()->share('headerCountries', $headerCountries);
            view()->share('headerCurrencies', $headerCurrencies);
            if ($setting) {
                if ($setting->commingsoon_mode) {
                    session()->put('commingsoon_mode', $setting->commingsoon_mode);
                }
            }
        }

        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });

            return $this;
        });
    }
}
