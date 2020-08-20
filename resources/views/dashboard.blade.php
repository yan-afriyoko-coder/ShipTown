@extends('layouts.app')

@section('title',__('Dashboard'))

@section('content')
<div class="container dashboard-widgets">
    <div class="row row-cols-sm-2 mb-2">
        <div class="col">@widget('ordersProcessing')</div>
        <div class="col">@widget('apt')</div>
    </div>
    <div class="row row-cols-sm-2 mb-2">
        <div class="col">@widget('PickCounts')</div>
        <div class="col"></div>
    </div>
    <div class="row">
        <div class="col">@widget('StatusOrderCount')</div>
    </div>
</div>
@endsection

