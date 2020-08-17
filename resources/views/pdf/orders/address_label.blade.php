@extends('pdf.template')
@section('content')
    {{ $order_number }}<br>
    Address line 1<br>
    Address line 2<br>
    Address line 3<br>
    <style>
        @page { size: 101mm 76mm;}
    </style>
@endsection

