@extends('layouts.app')

@section('title', __('Packsheet'))

@section('content')
    <div class="container">
        @if (session('status'))
            <div class="row">
                <div class="col">
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                </div>
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <order-packsheet-page :order_number="'{{ $order_number }}'"></order-packsheet-page>
            </div>
        </div>
    </div>
@endsection
