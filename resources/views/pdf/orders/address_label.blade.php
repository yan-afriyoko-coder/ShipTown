@extends('pdf.template')
@section('content')
    <div>{{ $shipping_address['company'] }}</div>
    <br>
    <div>{{ $shipping_address['first_name'] }} {{ $shipping_address['last_name'] }}</div>
    <div>{{ $shipping_address['address1'] }}</div>
    <div>{{ $shipping_address['address2'] }}</div>
    <div>{{ $shipping_address['city'] }} {{ $shipping_address['postcode'] }}</div>
    <div style="font-weight: bold">{{ $shipping_address['country_name'] }}</div>
    <br>
    <div style="font-size: xx-small">T: {{ $shipping_address['phone'] }}</div>
    <div style="font-size: xx-small">Order #: {{ $order_number }}</div>
    <style>
        @page { size: 101mm 76mm;}
    </style>
@endsection

