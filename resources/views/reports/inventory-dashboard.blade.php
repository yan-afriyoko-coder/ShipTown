@extends('layouts.app')

@section('title',__('Inventory Dashboard'))

@section('content')
    <div class="container dashboard-widgets">
    <div class="row">
        <div class="col">
            <div class="widget-tools-container">
{{--                <a class="btn btn-primary btn-sm mt-2 fa-arrow-alt-circle-down"  href="{{ request()->fullUrlWithQuery(['filename' =>  __($report_name).'.csv']) }}">{{ __('Download') }}</a>--}}
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">{{ __($report_name) }}</h4>


                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <table class="table table-borderless">
                                <thead>
                                <tr>
                                    <th scope="col">Products No Restock Levels</th>
                                    <th scope="col" class="text-right"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data as $record)
                                    <tr>
                                        <td><a href='{{ url()->route('reports.restocking', [
                                                        'select' => 'warehouse_code,product_sku,product_name,quantity_required,quantity_available,quantity_incoming,reorder_point,restock_level,warehouse_quantity',
                                                        'sort' => '-warehouse_quantity',
                                                        'per_page' => '999',
                                                        'filter[warehouse_code]' => data_get($record, 'warehouse_code'),
                                                        'filter[restock_level]' => 0,
                                                        'filter[inventory_source_warehouse_code]' => '99',
                                                        'filter[warehouse_quantity_between]' => '1,99999',
                                                    ]) }}'>
                                                {{ data_get($record, 'warehouse_code') }}
                                            </a>
                                        </td>
                                        <td class="text-right">{{ data_get($record, 'missing_restock_levels') === 0 ? '-' : data_get($record, 'missing_restock_levels')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <table class="table table-borderless">
                                <thead>
                                <tr>
                                    <th scope="col">Products Out Of Stock</th>
                                    <th scope="col" class="text-right"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data as $record)
                                    <tr>
                                        <td><a href='{{ url()->route('reports.restocking', [
                                                        'select' => 'warehouse_code,product_sku,product_name,quantity_required,quantity_available,quantity_incoming,reorder_point,restock_level,warehouse_quantity',
                                                        'sort' => 'quantity_available',
                                                        'per_page' => '999',
                                                        'filter[warehouse_code]' => data_get($record, 'warehouse_code'),
                                                        'filter[quantity_available]' => 0,
                                                        'filter[inventory_source_warehouse_code]' => '99',
                                                        'filter[warehouse_quantity_between]' => '1,99999',
                                                    ]) }}'>
                                                {{ data_get($record, 'warehouse_code') }}
                                            </a>
                                        </td>
                                        <td class="text-right">{{ data_get($record, 'wh_products_out_of_stock') === 0 ? '-' : data_get($record, 'wh_products_out_of_stock')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <table class="table table-borderless">
                                <thead>
                                <tr>
                                    <th scope="col">Products On Minus</th>
                                    <th scope="col" class="text-right"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data as $record)
                                    <tr>
                                        <td>
                                            <a href='{{ url()->route('reports.restocking', [
                                                        'select' => 'warehouse_code,product_sku,product_name,quantity_required,quantity_available,quantity_incoming,reorder_point,restock_level,warehouse_quantity',
                                                        'sort' => 'quantity_available',
                                                        'per_page' => '999',
                                                        'filter[warehouse_code]' => data_get($record, 'warehouse_code'),
                                                        'filter[quantity_available_between]' => '-9999,-0.01',
                                                        'filter[inventory_source_warehouse_code]' => '99',
                                                        'filter[warehouse_quantity_between]' => '1,99999',
                                                    ]) }}'>
                                                {{ data_get($record, 'warehouse_code') }}
                                            </a>
                                        </td>
                                        <td class="text-right">{{ data_get($record, 'products_on_minus') === 0 ? '-' : data_get($record, 'products_on_minus')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <table class="table table-borderless">
                                <thead>
                                <tr>
                                    <th scope="col">Products Required</th>
                                    <th scope="col" class="text-right"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data as $record)
                                    <tr>
                                        <td>
                                            <a href='{{ url()->route('reports.restocking', [
                                                        'select' => 'warehouse_code,product_sku,product_name,quantity_required,quantity_available,quantity_incoming,reorder_point,restock_level,warehouse_quantity',
                                                        'sort' => 'quantity_required',
                                                        'per_page' => '999',
                                                        'filter[warehouse_code]' => data_get($record, 'warehouse_code'),
                                                        'filter[quantity_required_between]' => '1,9999',
                                                        'filter[inventory_source_warehouse_code]' => '99',
                                                        'filter[warehouse_quantity_between]' => '1,99999',
                                                    ]) }}'>
                                                {{ data_get($record, 'warehouse_code') }}
                                            </a>
                                        </td>
                                        <td class="text-right">{{ data_get($record, 'wh_products_required') === 0 ? '-' : data_get($record, 'wh_products_required')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <table class="table table-borderless">
                                <thead>
                                <tr>
                                    <th scope="col">Products Restockable</th>
                                    <th scope="col" class="text-right"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data as $record)
                                    <tr>
                                        <td>
                                            <a href='{{ url()->route('reports.restocking', [
                                                        'select' => 'warehouse_code,product_sku,product_name,quantity_required,quantity_available,quantity_incoming,reorder_point,restock_level,warehouse_quantity',
                                                        'sort' => '-quantity_required,quantity_available',
                                                        'per_page' => '999',
                                                        'filter[warehouse_code]' => data_get($record, 'warehouse_code'),
                                                        'filter[inventory_source_warehouse_code]' => '99',
                                                        'filter[warehouse_quantity_between]' => '1,99999',
                                                    ]) }}'>
                                                {{ data_get($record, 'warehouse_code') }}
                                            </a>
                                        </td>
                                        <td class="text-right">{{ data_get($record, '`wh_products_available`') === 0 ? '-' : data_get($record, 'wh_products_available')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
