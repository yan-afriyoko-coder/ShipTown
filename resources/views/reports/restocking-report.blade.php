@extends('layouts.app')

@section('title',__('Restocking'))

@section('content')

    <restocking-page :initial_data="{{$initial_data}}"></restocking-page>

@endsection
