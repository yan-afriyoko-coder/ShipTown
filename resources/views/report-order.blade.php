@extends('layouts.app')

@section('title',__('Order Report'))

@section('content')
    <report-order
        meta-string="{{ json_encode($meta) }}"
        record-string="{{ json_encode($data) }}"
        download-button-text="{{ __('Download All') }}"
    ></report-order>
@endsection
