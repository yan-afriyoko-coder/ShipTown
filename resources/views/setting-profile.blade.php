@extends('layouts.app')

@section('title', __('Settings'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <user-courier-integration-select></user-courier-integration-select>

            <printer-configuration></printer-configuration>

        </div>
    </div>
</div>
@endsection
