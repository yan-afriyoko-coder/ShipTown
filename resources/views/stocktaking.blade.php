@extends('layouts.app')

@section('title', __('Stocktake'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
{{--                <order-packsheet-page :order_id="'{{ $order_id }}'"></order-packsheet-page>--}}
                <stocktaking-page></stocktaking-page>
            </div>
        </div>
    </div>
@endsection
