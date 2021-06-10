@extends('layouts.app')

@section('title', __('Settings'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card">
                <a href="{{ route('settings.general') }}">
                    <div class="title">General</div>
                    <div class="swiper-button-black">View and update general application settings</div>
                </a>
            </div>

            <div class="card">
                <a href="{{ route('settings.printnode') }}">
                    <div class="title">PrintNode</div>
                    <div class="swiper-button-black">View and update PrintNode integration  settings</div>
                </a>
            </div>

            <div class="card">
                <a href="{{ route('settings.rmsapi') }}">
                    <div class="title">Microsoft Dynamic RMS 2.0 API</div>
                    <div class="swiper-button-black">View and update RMS API integration settings</div>
                </a>
            </div>

            <div class="card">
                <a href="{{ route('settings.dpd-ireland') }}">
                    <div class="title">DPD Ireland API</div>
                    <div class="swiper-button-black">View and update DPD Ireland integration settings</div>
                </a>
            </div>

            <div class="card">
                <a href="{{ route('settings.api2cart') }}">
                    <div class="title">Api2cart</div>
                    <div class="swiper-button-black">View and update Api2cart integration settings</div>
                </a>
            </div>

            <div class="card">
                <a href="{{ route('settings.api') }}">
                    <div class="title">API</div>
                    <div class="swiper-button-black">View and update application API settings and tokens</div>
                </a>
            </div>
    </div>
</div>
@endsection
