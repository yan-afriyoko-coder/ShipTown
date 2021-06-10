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
            <printnode-configuration></printnode-configuration>
    </div>
</div>
@endsection
