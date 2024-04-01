@extends('pdf.template')
@section('content')
    <div>{{ $billling_address['company'] }}</div>
    <div>{{ $billling_address['first_name'] }} {{ $billling_address['last_name'] }}</div>
    <div>{{ $billling_address['address1'] }}</div>
    <div>{{ $billling_address['address2'] }}</div>
    <div>{{ $billling_address['city'] }} {{ $billling_address['postcode'] }}</div>
    <div>{{ $billling_address['state_name'] }}</div>
    <div style="font-weight: bold">{{ $billling_address['country_name'] }}</div>
    <div style="font-size: xx-small">T: {{ $billling_address['phone'] }}</div>
    <br>
    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($order_number, 'C39', 1,15) }}" alt="barcode" />
    <div style="font-size: xx-small">Order #{{ $order_number }}</div>

    <style>
        @page {
            size: 101.6mm 76.2mm;
            margin: 3mm;
        }
    </style>
@endsection

