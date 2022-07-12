@extends('layouts.app')

@section('title',__('Inventory Report'))

@section('content')
    <div class="container dashboard-widgets">
        <div class="row mb-3 pl-1 pr-1">
            <div class="flex-fill">
                <stocktaking-input-field placeholder="Search products using name, sku, alias or command"></stocktaking-input-field>
            </div>

            <button id="config-button" disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>

    <div class="row">

        <div class="col">
            <div class="widget-tools-container">
                <a class="btn btn-primary btn-sm mt-2 fa-arrow-alt-circle-down"  href="{{ request()->fullUrlWithQuery(['filename' =>  __($report_name).'.csv']) }}">{{ __('Download') }}</a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">{{ __($report_name) }}</h4>
                    <table class="table-hover w-100">
                        <thead>
                        <tr>
                            @foreach($fields as $field)
                                <th>{{ __($field) }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $record)
                            <tr class="table-hover">
                                @foreach($fields as $field)
                                    <td>{{ data_get($record, $field) }}</td>
                                @endforeach
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
