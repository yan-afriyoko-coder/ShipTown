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

                    <user-courier-integration-select></user-courier-integration-select>
                    @role('admin')
                        <maintenance-section></maintenance-section>
                        <auto-pilot-tuning-section></auto-pilot-tuning-section>
                    @endrole
    </div>
</div>
@endsection
