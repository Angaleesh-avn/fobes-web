@extends('frontend.layouts.app_resdex')

@section('title')
    {{ __('plan') }}
@endsection

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <x-website.company.sidebar2 />
                <div class="col-lg-9">
                    <div class="dashboard-right">
                        <div class="row tw-my-5">
                            <div class="col-lg-5">
                                <div class="plan-card">
                                    <h2 class="title">{{ $userplan->label }}</h2>
@php
use Carbon\Carbon;
$expiryDate = Carbon::parse($userPlanResdex->plan_expired_at);
$formattedDate = $expiryDate->format('d-m-Y');
@endphp
@if (isset($userPlanResdex) && $expiryDate < Carbon::now())
    <span style="color:#d3a58e;">Plan Expired</span>
@else
    <span style="color:#d3a58e;"><small>Your Plan will Expire on {{ $formattedDate }}</small></span>
@endif

                                    <div class="">
                                        <a href="{{ route('website.plan') }}" class="btn btn-primary">
                                            {{ __('upgrade_plan') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="benefits-card">
                                    <h4 class="title">{{ __('create_plan_benefits') }}</h4>
                                    <ul class="benefit-list">
                                        <li>
                                            <x-svg.double-check-icon />
                                            <span>{{ $userplan->credits }} Credits</span>
                                        </li>
                                    </ul>
                                    <div class="remaining">
                                        <h4 class="title">{{ __('remaining') }}</h4>
                                        <ul class="remaining-list">
                                            <li>
                                                @if (!$userPlanResdex->credit_limit)
                                                    <x-svg.cross-round2-icon />
                                                @else
                                                    <x-svg.double-check-icon />
                                                @endif
                                                <span>{{ $userPlanResdex->credit_limit }} Credits Left</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoices-table ">
                            <h2 class="title">{{ __('latest_invoice') }}</h2>
                            <div class="table-wrapper pb-1">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('date') }}</th>
                                            <th>{{ __('plan') }}</th>
                                            <th>{{ __('amount') }}</th>
                                            <th>{{ __('payment_provider') }}</th>
                                            <th>{{ __('payment_status') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($transactions->count() > 0)
                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td>#{{ $transaction->order_id }}</td>
                                                    <td>{{ formatTime($transaction->created_at, 'M, d Y') }}</td>
                                                    <td>
                                                        @if ($transaction->payment_type == 'per_job_based')
                                                            <span
                                                                class="badge bg-secondary">{{ ucfirst(Str::replace('_', ' ', $transaction->payment_type)) }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-primary">{{ $transaction->plan->label }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <!--{{ currencyConversion($transaction->usd_amount, 'USD', currentCurrencyCode() ?? 'USD') }}-->
                                                        {{ $transaction->amount }}
                                                        {{ currentCurrencyCode() }}
                                                    </td>
                                                    <td>
                                                        @if ($transaction->payment_provider == 'offline')
                                                            {{ __('offline') }}
                                                            @if (isset($transaction->manualPayment) && isset($transaction->manualPayment->name))
                                                                (<b>{{ $transaction->manualPayment->name }}</b>)
                                                            @endif
                                                        @else
                                                            {{ ucfirst($transaction->payment_provider) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($transaction->payment_status == 'paid')
                                                            <span
                                                                class="badge badge-pill bg-success">{{ __('paid') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-pill bg-warning">{{ __('unpaid') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="tw-inline-flex tw-gap-2 tw-items-center">
                                                            <form
                                                            action="{{ route('company.transaction.invoice.download.resdex', $transaction->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn tw-p-0">
                                                                <x-svg.download-icon />
                                                            </button>
                                                        </form>
                                                        <a
                                                            href="{{ route('company.transaction.invoice.view.resdex', $transaction->order_id) }}">
                                                            {{ __('view_invoice') }}
                                                        </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <x-website.not-found />
                                        @endif
                                    </tbody>
                                </table>
                                @if (request('perpage') != 'all' && $transactions->total() > $transactions->count())
                                    <div class="rt-pt-30 mb-3">
                                        <nav>
                                            {{ $transactions->onEachSide(0)->links('vendor.pagination.frontend') }}
                                        </nav>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
