@extends('layouts.app')

@section('title',__('Dashboard'))

@section('content')
<div class="container dashboard-widgets">



    <div class="row row-cols-sm-2 mb-2">
        <div class="col">@widget('ordersProcessing')</div>
        <div class="col">@widget('apt')</div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col">
                            <div class="">@widget('ActiveOrdersWidget')</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="">@widget('StatusOrderCount')</div>
                        </div>
                    </div>

                    @role('admin')

                    <div class="row">
                        <div class="col">
                            <div class="">@widget('UserPickCounts')</div>
                        </div>
                    </div>

                    @endrole

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

