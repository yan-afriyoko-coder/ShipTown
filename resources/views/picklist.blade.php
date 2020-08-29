@extends('layouts.app')

@section('title', __('Picklist'))

@section('content')
<div class="container">
    @if (session('status'))
    <div class="row">
        <div class="col">
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        </div>
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-lg-12">
{{--            <picklist-table></picklist-table>--}}
            <picks-table></picks-table>
        </div>
    </div>
</div>
@endsection

