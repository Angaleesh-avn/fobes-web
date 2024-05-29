@extends('frontend.layouts.app')
<style>
    .box-border{
        border: 1px solid #254fcd !important;
    }
</style>

@section('description')
    @php
        $data = metaData('pricing');
    @endphp
    {{ $data->description }}
@endsection
@section('og:image')
    {{ asset($data->image) }}
@endsection
@section('title')
    {{ $data->title }}
@endsection
@section('main')
    <div class="breadcrumbs-custom breadcrumbs-height">
        <div class="container">
            <div class="row align-items-center breadcrumbs-height">
                <div class="col-12 justify-content-center text-center">
                    <div class="breadcrumb-title rt-mb-10"> {{ __('pricing') }}</div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="{{ route('website.home') }}">{{ __('home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> {{ __('pricing') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="terms-condition ">
        <div class="container">
            <div class="pricing-options tw-justify-between">
                <div class="choose-pricing">
                    <h2>{{ __('buy_premium_subscription_to_post_job') }}</h2>
                    <p>{{ __('elevate_your_job_postings_with_a_premium_subscription_get_expanded_reach_targeted_promotion_and_advanced_applicant_filtering') }}
                    </p>
                    <a href="#premium_pricing_package">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.875 13.75L8.125 17.5L4.375 13.75" stroke="#0A65CC" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M15.625 2.5C13.6359 2.5 11.7282 3.29018 10.3217 4.6967C8.91518 6.10322 8.125 8.01088 8.125 10V17.5"
                                stroke="#0A65CC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('choose_a_pricing_plan_from_below') }}
                    </a>
                </div>
                <div class="choose-pricing">
                    <h2>RESUME DATABASE ACCESS</h2>
                    <p>Resdex - India's largest resume database for all your hiring needs</p>
                    <a href="#premium_pricing_package-resdex">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.875 13.75L8.125 17.5L4.375 13.75" stroke="#0A65CC" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M15.625 2.5C13.6359 2.5 11.7282 3.29018 10.3217 4.6967C8.91518 6.10322 8.125 8.01088 8.125 10V17.5"
                                stroke="#0A65CC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Select your resdex plan
                    </a>
                </div>
                @if ($setting->per_job_active)
                    <div class="pay-per-job">
                        <h2 class="">{{ __('Or_pay_per_job_post') }}</h2>
                        <p>{{ __('pay_only_for_a_single_job_creation_and_you_can_also_highlight_or_featured_them') }}</p>
                        @auth('user')
                            <a href="{{ route('company.job.payPerJobCreate') }}"
                                class="btn btn-primary">{{ __('pay_per_job') }}</a>
                        @else
                            <a href="{{ route('company.job.payPerJobCreate') }}"
                                class="btn btn-primary login_required">{{ __('pay_per_job') }}</a>
                        @endauth
                    </div>
                @endif

            </div>
            @if ($plans->count() > 0)
                <div class="row justify-content-center text-center" id="premium_pricing_package">
                    <div class="col-12">
                        <div class="rt-spacer-100 rt-spacer-md-50"></div>
                        <h4 class="rt-mb-18">
                            {{ __('choose_plan') }}
                        </h4>
                        <div class="body-font-3 text-gray-500 rt-mb-24 max-474 d-inline-block">
                            {{ __('choose_plan_description') }}
                        </div>
                    </div>
                </div>
            @endif
            <section class="pricing-area mt-5" id="premium_pricing_package">
                <div class="row">
                    @forelse ($plans as $plan)
                        @if ($plan->frontend_show)
                            <div class="col-xl-4 col-lg-4 col-md-6 rt-mb-24">
                                <div class="single-price-table mb-4 mb-md-0 {{ $plan->recommended ? 'active' : '' }}">
                                    <div class="price-header">
                                        <h6 class="rt-mb-10">{{ $plan->label }}</h6>
                                        @if ($plan->plan_valid_days)
                                        <span style="color:#3e66ff;"> {{ $plan->plan_valid_days }}  days Validity</span>
                                        @endif
                                        @if ($plan->recommended)
                                            <span class="badge bg-primary-500 text-white">{{ __('recommanded') }}</span>
                                        @endif
                                        <span class="text-gray-500 body-font-3 rt-mb-15 d-block">
                                            @if (isset($plan->descriptions) && isset($plan->descriptions[0]))
                                                {!! $plan->descriptions[0]->description !!}
                                            @else
                                                @php
                                                    $default_description = $plan_descriptions->where('plan_id', $plan->id)->first();
                                                @endphp

                                                @if ($default_description && $default_description->description)
                                                    {!! $default_description->description !!}
                                                @else
                                                    <span class="text-danger">{!! __('no_description_has_been_added_to_this_language', ['current' => $current_language_code]) !!}</span>
                                                @endif
                                            @endif
                                        </span>
                                        <div>
                                            @if (isset($plan->strikethrough_price) && $plan->strikethrough_price!="")
                                            <span
                                                class="tw-text-[36px] tw-leading-[44px] tw-font-medium">
                                                <del> {{ currencyPosition($plan->strikethrough_price, true,$current_currency) }}</del>
                                            </span>
                                            @endif
                                            <span
                                                class="tw-text-[#0A65CC] tw-text-[36px] tw-leading-[44px] tw-font-medium">
                                                {{ currencyPosition($plan->price, true,$current_currency) }}
                                            </span>
                                            @if (isset($plan->price) && $plan->price >= 1)
                                            <small style='color:red;'>+ 18% GST</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="price-body">
                                        <ul class="rt-list">
                                            <li>
                                                <span class="tw-inline-flex tw-justify-center tw-items-center tw-w-6 tw-h-6 tw-rounded-full tw-bg-[#eef5fc]">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="#007BFF"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span>
                                                    {{ __('post') }} <b>{{ $plan->job_limit }}</b>
                                                    {{ __('jobs') }}
                                                </span>
                                            </li>

                                            <li>
                                                <span class="tw-inline-flex tw-justify-center tw-items-center tw-w-6 tw-h-6 tw-rounded-full tw-bg-[#eef5fc]">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="#007BFF"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span><b>{{ $plan->featured_job_limit }}</b>
                                                    {{ __('featured_job') }}</span>
                                            </li>
                                            <li>
                                                <span class="tw-inline-flex tw-justify-center tw-items-center tw-w-6 tw-h-6 tw-rounded-full tw-bg-[#eef5fc]">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="#007BFF"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span><b>{{ $plan->highlight_job_limit }}</b>
                                                    {{ __('highlights_job') }}</span>
                                            </li>
                                            <li>
                                                <span class="tw-inline-flex tw-justify-center tw-items-center tw-w-6 tw-h-6 tw-rounded-full tw-bg-[#eef5fc]">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="#007BFF"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span>
                                                    <b>{{ $plan->candidate_cv_view_limitation == 'limited' ? $plan->candidate_cv_view_limit : __('unlimited') }}</b>
                                                    {{ __('candidate_profile_view') }}
                                                </span>
                                            </li>
                                            @if(($plan->id == 2) || ($plan->id == 3))
                                            <li>
                                                <span class="tw-inline-flex tw-justify-center tw-items-center tw-w-6 tw-h-6 tw-rounded-full tw-bg-[#eef5fc]">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="#007BFF"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span> Job Branding </span>
                                               
                                            </li>
                                            @else
                                            <li>
                                                <span class="rounded-full text-white ">

                                                        <svg width="32" height="32" style="margin-left: -6px"
                                                            xmlns="http://www.w3.org/2000/svg" fill="#FF7276"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </span>
                                                <span> Job Branding </span>
                                               
                                            </li>
                                            @endif
                                            @if($plan->id == 3)
                                            <li>
                                                <span class="tw-inline-flex tw-justify-center tw-items-center tw-w-6 tw-h-6 tw-rounded-full tw-bg-[#eef5fc]">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="#007BFF"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span> Smart Boost Via Whatsapp </span>
                                               
                                            </li>
                                            <li>
                                                <span class="tw-inline-flex tw-justify-center tw-items-center tw-w-6 tw-h-6 tw-rounded-full tw-bg-[#eef5fc]">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="#007BFF"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span> Get Noticed with 'Urgent Hiring' tag</span>
                                               
                                            </li>
                                            @else
                                            <li>
                                                <span class="rounded-full text-white ">

                                                        <svg width="32" height="32" style="margin-left: -6px"
                                                            xmlns="http://www.w3.org/2000/svg" fill="#FF7276"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </span>
                                                <span> Smart Boost Via Whatsapp </span>
                                               
                                            </li>
                                            <li>
                                                <span class="rounded-full text-white ">

                                                        <svg width="32" height="32" style="margin-left: -6px"
                                                            xmlns="http://www.w3.org/2000/svg" fill="#FF7276"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </span>
                                                <span> Get Noticed with 'Urgent Hiring' tag</span>
                                               
                                            </li>
                                            @endif
                                            <li style="display: flex ;align-items: center ">
                                                @if ($plan->profile_verify)
                                                    <span>
                                                        <img src="{{ asset('frontend') }}/assets/images/icon/check.png"
                                                            alt="">
                                                    </span>
                                                @else
                                                    <span class="rounded-full text-white ">

                                                        <svg width="32" height="32" style="margin-left: -6px"
                                                            xmlns="http://www.w3.org/2000/svg" fill="#FF7276"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </span>
                                                @endif
                                                <span>
                                                    {{ __('ability_to_profile_verify') }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="price-footer">
                                        @auth('user')
                                            @if ($plan->price == 0)
                                                <form action="{{ route('purchase.free.plan') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" class="d-none tw-hidden" name="plan"
                                                        value="{{ $plan->id }}" readonly>
                                                    <button class="btn btn-primary-50 d-block">
                                                        <span class="button-content-wrapper ">
                                                            <span class="button-icon align-icon-right">
                                                                <i class="ph-arrow-right"></i>
                                                            </span>
                                                            <span class="button-text">
                                                                {{ __('get_started') }}
                                                            </span>
                                                        </span>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('website.plan.details', $plan->label) }}"
                                                    class="btn btn-primary-50 d-block">
                                                    <span class="button-content-wrapper ">
                                                        <span class="button-icon align-icon-right">
                                                            <i class="ph-arrow-right"></i>
                                                        </span>
                                                        <span class="button-text">
                                                            {{ __('get_started') }}
                                                        </span>
                                                    </span>
                                                </a>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-primary-50 d-block login_required">
                                                <span class="button-content-wrapper ">
                                                    <span class="button-icon align-icon-right">
                                                        <i class="ph-arrow-right"></i>
                                                    </span>
                                                    <span class="button-text">
                                                        {{ __('get_started') }}
                                                    </span>
                                                </span>
                                            </button>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="col-md-12">
                            <div class="card text-center">
                                <x-not-found message="{{ __('no_price_plan_found_contact_website_owner') }}" />
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>
            
            @if ($resdex_plans->count() > 0)
            <div class="row justify-content-center text-center" id="premium_pricing_package-resdex">
                    <div class="col-12">
                        <div class="rt-spacer-100 rt-spacer-md-50"></div>
                        <h4 class="rt-mb-18">
                            Select Your Resdex Plan
                        </h4>
                        <div class="body-font-3 text-gray-500 rt-mb-24 max-474 d-inline-block">
                            Resdex - India's largest resume database for all your hiring needs
                        </div>
                    </div>
            </div>
            @endif
            <section class="pricing-area mt-5" id="premium_pricing_package-resdex">
                <div class="row">
                    @forelse ($resdex_plans as $resdex_plan)
                        @if ($resdex_plan->frontend_show)
                        <div class="col-xl-4 col-lg-4 col-md-6 rt-mb-24">
                            <div class="single-price-table mb-4 mb-md-0 box-border">
                                <div class="price-header">
                                        <h6 class="rt-mb-10">{{ $resdex_plan->label }}</h6>
                                        @if ($resdex_plan->plan_valid_days)
                                        <span style="color:#3e66ff;"> {{ $resdex_plan->plan_valid_days }}  days Validity</span>
                                        @endif
                                        <span class="text-gray-500 body-font-3 rt-mb-15 d-block">
                                         Plan Description
                                        </span>
                                        <div>
                                            @if (isset($resdex_plan->strikethrough_price) && $resdex_plan->strikethrough_price!="")
                                            <span
                                                class="tw-text-[36px] tw-leading-[44px] tw-font-medium">
                                                <del> {{ currencyPosition($resdex_plan->strikethrough_price, true,$current_currency) }}</del>
                                            </span>
                                            @endif
                                            <span
                                                class="tw-text-[#0A65CC] tw-text-[36px] tw-leading-[44px] tw-font-medium">
                                                {{ currencyPosition($resdex_plan->price, true,$current_currency) }}
                                            </span>
                                            @if (isset($resdex_plan->price) && $resdex_plan->price >= 1)
                                            <small style='color:red;'>+ 18% GST</small>
                                            @endif
                                        </div>
                                </div>
                                <div class="price-body">
                                        <ul class="rt-list">
                                            <li>
                                                <span class="tw-inline-flex tw-justify-center tw-items-center tw-w-6 tw-h-6 tw-rounded-full tw-bg-[#eef5fc]">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="#007BFF"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span>
                                                    {{ $resdex_plan->credits }} CV views
                                                </span>
                                            </li>
                                        </ul>
                                </div>
                                <div class="price-footer">
                                    @auth('user')
                                    <a href="{{ route('website.plan_resdex.details', $resdex_plan->label) }}" class="btn btn-primary-50 d-block">
                                        <span class="button-content-wrapper ">
                                            <span class="button-icon align-icon-right">
                                                <i class="ph-arrow-right"></i>
                                            </span>
                                            <span class="button-text">
                                                {{ __('get_started') }}
                                            </span>
                                        </span>
                                    </a>
                                    @else
                                        <button type="button" class="btn btn-primary-50 d-block login_required">
                                            <span class="button-content-wrapper ">
                                                <span class="button-icon align-icon-right">
                                                    <i class="ph-arrow-right"></i>
                                                </span>
                                                <span class="button-text">
                                                    {{ __('get_started') }}
                                                </span>
                                            </span>
                                        </button>
                                    @endauth
                                </div>
                            </div>
                    </div>
                        @endif
                    @empty
                    <div class="col-md-12">
                            <div class="card text-center">
                                <x-not-found message="{{ __('no_price_plan_found_contact_website_owner') }}" />
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
    <div class="rt-spacer-75 rt-spacer-md-30"></div>

    {{-- Subscribe Newsletter --}}
    <x-website.subscribe-newsletter />
@endsection
@section('css')
    <style>
        .breadcrumbs-custom {
            padding: 20px;
            background-color: var(--gray-20);
            transition: all 0.24s ease-in-out;
        }

        .pricing-options {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-top: 48px;
        }

        @media (max-width: 991px) {
            .pricing-options {
                flex-direction: column;
            }
        }

        .pricing-options .choose-pricing h2 {
            font-weight: 500;
            font-size: 24px;
            line-height: 32px;
            color: #18191C;
            margin-bottom: 16px;
        }

        .pricing-options .choose-pricing p {
            font-weight: 400;
            font-size: 16px;
            line-height: 24px;
            color: #5E6670;
            margin-bottom: 16px;
        }

        .pricing-options .choose-pricing a {
            font-weight: 600;
            font-size: 16px;
            line-height: 24px;
            text-align: justify;
            text-transform: capitalize;
            color: #0A65CC;
        }

        .pricing-options .pay-per-job {
            background: rgba(241, 242, 244, 0.6);
            border: 1px solid #E4E5E8;
            border-radius: 6px;
            padding: 24px;
        }

        .pricing-options .pay-per-job h2 {
            font-weight: 600;
            font-size: 18px;
            line-height: 24px;
            text-transform: uppercase;
            color: #18191C;
            margin-bottom: 12px;
        }

        .pricing-options .pay-per-job p {
            font-weight: 400;
            font-size: 14px;
            line-height: 20px;
            color: #474C54;
            margin-bottom: 16px;
        }
    </style>
@endsection
