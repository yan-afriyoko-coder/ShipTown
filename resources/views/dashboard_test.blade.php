@extends('layouts.app')

@section('title',__('Dashboard'))

@section('content')
<div class="container dashboard-widgets">

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="">@widget('TimeToZeroWidget')</div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>
@endsection

