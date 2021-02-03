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

                    <settings-page></settings-page>
                    @role('admin')
                        <passport-clients></passport-clients>
                        <passport-authorized-clients></passport-authorized-clients>
                        <passport-personal-access-tokens></passport-personal-access-tokens>
                        <api2cart-configuration></api2cart-configuration>
                        <rmsapi-configuration></rmsapi-configuration>
                        <printnode-configuration></printnode-configuration>
                    @endrole
                    <printer-configuration></printer-configuration>
    </div>
</div>
@endsection
