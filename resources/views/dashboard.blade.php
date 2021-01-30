@extends('layouts.app')

@section('title',__('Dashboard'))

@section('content')
<div class="container dashboard-widgets">

    <div class="row">
        <div class="col card">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@widget('DateSelectorWidget', ['between_dates' => Request::get('between_dates') ])</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@widget('PackedTodayByUser', ['between_dates' => Request::get('between_dates') ])</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@asyncWidget('ActiveOrdersWidget')</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@asyncWidget('ToFollowStatusOrderCountWidget')</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@asyncWidget('OrdersByAgeWidget')</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@asyncWidget('TimeToZeroWidget')</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@asyncWidget('ProductsPackedTodayCountsWidget')</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@asyncWidget('ProductsPickedTodayCountsWidget')</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="">@asyncWidget('CompletedStatusOrderCount')</div>
                    </div>
                </div>

                @role('admin')

                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="">@asyncWidget('apt')</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="">@asyncWidget('PacksStatisticsWidget')</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="">@asyncWidget('NewOrdersCounts')</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="">@asyncWidget('CompletedOrdersCountByDateWidget')</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="">@asyncWidget('UserPickCounts')</div>
                        </div>
                    </div>

                @endrole

            </div>
        </div>
    </div>

</div>
@endsection

