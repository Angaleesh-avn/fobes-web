@extends('frontend.layouts.app')

@section('description')
    @php
    $data = metaData('abroad-jobs');
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
    
        <div class="container">
            <img src="{{asset('frontend/assets/images/coming-soon.png')}}" alt="Coming Soon">
        </div>

    @endsection

    @section('css')
        <style>
            .brand-img-size {
                max-width: 100% !important;
                height: auto !important;
                max-width: 250px !important;
            }
        </style>
    @endsection


