@extends('layouts.app')

@section('title',__('Dashboard'))

@section('content')
<div class="container dashboard-widgets">

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <div class="col">
                            <div class="">@widget('OrdersByAgeWidget')</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="">@widget('ActiveOrdersWidget')</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="">@widget('ToFollowStatusOrderCountWidget')</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="">@widget('CompletedStatusOrderCount')</div>
                        </div>
                    </div>


                    @role('admin')

                        <div class="row">
                            <div class="col">
                                <h4 class="card-title text-center">PACKING STATISTICS</h4>
                                <h6 class="card-title text-center">LAST 7 DAYS</h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="">@widget('apt')</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="">@widget('PacksStatisticsWidget')</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="">@widget('NewOrdersCounts')</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="">@widget('CompletedOrdersCountByDateWidget')</div>
                            </div>
                        </div>

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

