@extends('layouts.app')

@section('title','Users')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <user-table></user-table>
        </div>
    </div>
</div>
@endsection
