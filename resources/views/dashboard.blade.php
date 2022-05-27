@extends('layouts.app')

@section('title',__('Dashboard'))

@section('content')
<div class="container dashboard-widgets">

{{--    <div class="row mb-3 pl-1 pr-1">--}}
{{--        <div class="flex-fill">--}}
{{--            <barcode-input-field :url_param_name="'search'" @commandEntered="" @barcodeScanned="" placeholder="Search products using name, sku, alias or command" ref="barcode"></barcode-input-field>--}}
{{--        </div>--}}

{{--        <button disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>--}}
{{--    </div>--}}

    <div class="row">
        <div class="col card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@widget('OrderPackedCountsByUser', ['between_dates' => Request::get('between_dates') ])</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@widget('OrdersActiveByStatusWidget')</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@asyncWidget('OrdersOnHoldByStatusWidget')</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@asyncWidget('OrdersByAgeWidget')</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

