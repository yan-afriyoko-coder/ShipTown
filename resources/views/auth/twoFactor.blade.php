@extends('layouts.auth')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Two Factor Verification') }}</div>

                <div class="card-body">
                    <p class="text-muted">
                        You have received an email which contains two factor login code.
                        If you haven't received it, press <a href="{{ route('verify.resend') }}">here</a>.
                    </p>

                    @if (session('message'))
                        <div class="alert alert-info" role="alert">
                            {{ session()->get('message') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verify.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="two_factor_code" class="col-md-4 col-form-label text-md-right">{{ __('Two Factor Code') }}</label>

                            <div class="col-md-6">
                                <input id="two_factor_code" type="text" class="form-control @error('two_factor_code') is-invalid @enderror" name="two_factor_code" value="{{ old('two_factor_code') }}" required autofocus>

                                @error('two_factor_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Verify') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
