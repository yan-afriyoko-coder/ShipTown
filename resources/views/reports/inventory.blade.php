@extends('layouts.app')

@section('title',__('Inventory Report'))

@section('content')
    <div class="container dashboard-widgets">
    <div class="row">
        <div class="col">
            <div class="widget-tools-container">
                <a class="btn btn-primary btn-sm mt-2 fa-arrow-alt-circle-down"  href="{{ request()->fullUrlWithQuery(['filename' => 'inventory.csv']) }}">{{ __('Download') }}</a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">{{ __('Inventory') }}</h4>
                    <table class="table-hover w-100">
                        <thead>
                        <tr>
                            <th>{{ __('Warehouse') }}</th>
                            <th>{{ __('SKU') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Quantity Available') }}</th>
                            <th>{{ __('Restock Level') }}</th>
                            <th>{{ __('Reorder Point') }}</th>
                            <th>{{ __('Quantity Required') }}</th>
                            <th>{{ __('Inventory Source') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $record)
                            <tr class="table-hover">
                                <td>{{ data_get($record, 'warehouse.code') }}</td>
                                <td><a href="/products?hide_nav_bar=true&search={{ data_get($record, 'product.sku') }}" target="_blank">{{ data_get($record, 'product.sku') }}</a></td>
                                <td>{{ data_get($record, 'product.name') }}</td>
                                <td>{{ data_get($record, 'quantity_available') }}</td>
                                <td>{{ data_get($record, 'restock_level') }}</td>
                                <td>{{ data_get($record, 'reorder_point') }}</td>
                                <td>{{ data_get($record, 'quantity_required') }}</td>
                                <td>{{ data_get($record, 'inventory_source_quantity_available') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
