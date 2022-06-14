<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @auth
    <meta name="user-id" content="{{ Auth::user()->id }}">
    <meta name="current-user" content="{{ \App\Http\Resources\UserResource::make(Auth::user())->toJson() }}">
    @endauth

    <title>PM @yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @yield('css')

    <link rel="manifest" href="/manifest.json">
</head>
<body>
    <div id="app">
        <heartbeats></heartbeats>

        @if((request()->input('hide_nav_bar', false) === false))
            @include('layouts.nav')
        @endif

        @if (session('alert-success-message'))
            <div class="alert alert-success" role="alert">{{ session('alert-success-message') }}</div>
        @endif

        @if (session('alert-warning-message'))
            <div class="alert alert-warning" role="alert">{{ session('alert-warning-message') }}</div>
        @endif

        @if (session('alert-danger-message'))
            <div class="alert alert-danger" role="alert">{{ session('alert-danger-message') }}</div>
        @endif

        <main class="py-0 pl-1 pr-2">
            @yield('content')
        </main>

        <vue-snotify></vue-snotify>
    </div>
</body>
</html>
