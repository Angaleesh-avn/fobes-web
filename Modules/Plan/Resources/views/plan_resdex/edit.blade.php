@extends('backend.layouts.app')
@section('title')
    {{ __('edit') }}
@endsection
@section('content')
    @if (userCan('plan.update'))
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title line-height-36">{{ __('edit') }} {{ __('plan') }}</h3>
                            <a href="{{ route('module.plan_resdex.index') }}"
                                class="btn bg-primary float-right d-flex align-items-center justify-content-center">
                                <i class="fas fa-arrow-left"></i>&nbsp; {{ __('back') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <form action="{{ route('module.plan_resdex.update', $plan->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="label">{{ __('label') }} <small
                                                            class="text-danger">*</small></label>
                                                    <input type="text" id="label" name="label" value="{{ $plan->label }}"
                                                        class="form-control @error('label') is-invalid @enderror"
                                                        placeholder="enter plan name">
                                                    @error('label')
                                                        <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="strikethroughprice">Strikethrough Price
                                                        {{ defaultCurrencySymbol() }}
                                                    </label>
                                                    <input type="number" id="strikethrough_price" name="strikethrough_price"
                                                        value="{{ $plan->strikethrough_price }}"
                                                        class="form-control"
                                                        placeholder="{{ __('10') }}{{ defaultCurrencySymbol() }}">
                                                        <small
                                                            class="text-danger">Keep it blank if no need strikethrough price</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="price">{{ __('price') }}
                                                        {{ defaultCurrencySymbol() }}<small
                                                            class="text-danger">*</small></label>
                                                    <input type="number" id="price" name="price" value="{{ $plan->price }}"
                                                        class="form-control @error('price') is-invalid @enderror">
                                                    @error('price')
                                                        <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="credits">Credit Limit <small
                                                            class="text-danger">*</small></label>
                                                    <input type="number" id="credits" name="credits"
                                                        value="{{ $plan->credits }}"
                                                        class="form-control @error('credits') is-invalid @enderror"
                                                        placeholder="{{ __('enter') }} credits">
                                                    @error('credits')
                                                        <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="frontend_show">
                                                        {{ __('show_frontend') }} <small class="text-danger">*</small>
                                                    </label> <br>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="show_frontend_yes" name="frontend_show" class="plan_type_selection custom-control-input" value="1" {{ $plan->frontend_show ? 'checked':'' }}>
                                                        <label class="custom-control-label" for="show_frontend_yes">{{ __('yes') }}</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="show_frontend_no" name="frontend_show" class="plan_type_selection custom-control-input" value="0" {{ !$plan->frontend_show ? 'checked':'' }}>
                                                        <label class="custom-control-label" for="show_frontend_no">
                                                            {{ __('no') }}
                                                        </label>
                                                    </div>
                                                    @error('frontend_show')
                                                        <span class="invalid-feedback" role="alert">{{ __($message) }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="plan_valid_days">Plan Valid Days
                                                        {{ defaultCurrencySymbol() }}<small class="text-danger">*</small>
                                                    </label>
                                                    <input type="number" id="plan_valid_days" name="plan_valid_days"
                                                        value="{{ $plan->plan_valid_days }}"
                                                        class="form-control"
                                                        placeholder="{{ __('10') }}{{ defaultCurrencySymbol() }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center">
                                            <button class="btn btn-success" type="submit">
                                                <i class="fas fa-sync"></i>&nbsp; {{ __('update') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection


@section('script')
    <script>
        checkSubscriptionType('{{ old("subscription_type", $plan->subscription_type) }}');

        $('.plan_type_selection').on('click', function(value){
            var value = $("[name='subscription_type']:checked").val();
            checkSubscriptionType(this.value);
        });

        function checkSubscriptionType(type){
            if (type == 'recurring') {
                $('#plan_interval').removeClass('d-none');
            }else{
                $('#plan_interval').addClass('d-none');
            }
        }

        profileViewLimitation('{{ old("candidate_cv_view_limitation", $plan->candidate_cv_view_limitation) }}');

        function profileViewLimitation(status){
            if (status == 'unlimited') {
                $('#candidate_profile_view_count_field').addClass('d-none');
            }else{
                $('#candidate_profile_view_count_field').removeClass('d-none');
            }
        }

        $('.candidate_profile_view').on('click', function(value){
            var value = $("[name='candidate_cv_view_limitation']:checked").val();
            profileViewLimitation(this.value);
        });
    </script>
@endsection
