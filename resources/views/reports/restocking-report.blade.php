@extends('layouts.app')

@section('title',__('Restocking'))

@section('content')
    <restocking-page></restocking-page>

    <div class="container dashboard-widgets">

        @foreach ($data as $record)
            <div class="row mb-3">
                <div class="col">
                    <div class="card">
                        <div class="card-body pt-2">
                            <div class="row mt-0">
                                <div class="col-lg-6">
                                    <div class="text-primary h5">{{ data_get($record, 'product_name')  }}</div>
                                    <div>
                                        sku: <b><a target="_blank" href="/products?hide_nav_bar=true&search={{ data_get($record, 'product_sku') }}">{{ data_get($record, 'product_sku') }}</a></b>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row pt-1">
                                        <div class="col-6 small">
                                            <div >
                                                incoming: <b>{{ data_get($record, 'quantity_incoming') }}</b>
                                            </div>
                                            <div >
                                                warehouse: <b>{{ data_get($record, 'warehouse_quantity') }}</b>
                                            </div>
                                            <div class=" {{ data_get($record, 'reorder_point') <= 0 ? 'bg-warning' : ''  }}">
                                                reorder_point: <b>{{ data_get($record, 'reorder_point') }}</b>
                                            </div>
                                            <div class=" {{ data_get($record, 'reorder_point') <= 0 ? 'bg-warning' : ''  }}">
                                                restock_level: <b>{{ data_get($record, 'restock_level') }}</b>
                                            </div>
                                            <div>
                                                warehouse_code: <b>{{ data_get($record, 'warehouse_code') }}</b>
                                            </div>
                                        </div>
                                        <div class="col-3 text-center {{ data_get($record, 'quantity_available') <= 0 ? 'bg-warning' : ''  }}">
                                            <small>available</small>
                                            <h3>{{ data_get($record, 'quantity_available') }}</h3>
                                        </div>
                                        <div class="col-3 text-center">
                                            <small>required</small>
                                            <h3>{{ data_get($record, 'quantity_required') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
