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

                    <printer-configuration></printer-configuration>
                    <user-courier-integration-select></user-courier-integration-select>
                    @role('admin')
                        <maintenance-section></maintenance-section>
                        <auto-pilot-tuning-section></auto-pilot-tuning-section>
                        <passport-clients></passport-clients>
                        <passport-authorized-clients></passport-authorized-clients>
                        <passport-personal-access-tokens></passport-personal-access-tokens>
                        <api2cart-configuration></api2cart-configuration>
                        <rmsapi-configuration></rmsapi-configuration>
                        <printnode-configuration></printnode-configuration>
                    @endrole
    </div>
</div>
@endsection
