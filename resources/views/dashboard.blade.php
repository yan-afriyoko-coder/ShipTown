@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row row-cols-sm-2">
        <div class="col">@widget('ordersProcessing')</div>
        <div class="col">@widget('apt')</div>
        <div class="col">@widget('StatusOrderCount')</div>
    </div>
</div>
@endsection

