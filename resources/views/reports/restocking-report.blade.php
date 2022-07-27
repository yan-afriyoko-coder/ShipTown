@extends('layouts.app')

@section('title',__('Restocking'))

@section('content')

    <restocking-page :initial_data="{{$data->resource}}"></restocking-page>

@endsection
