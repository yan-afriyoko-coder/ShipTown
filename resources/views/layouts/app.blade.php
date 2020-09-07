<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @auth
    <meta name="user-id" content="{{ Auth::user()->id }}">
    @endauth

    <title>PM @yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="manifest" href="/manifest.json">
</head>
<body>
    <div id="app">
        @auth
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    PM
                    @yield('title')
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">|</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products') }}">{{ __('Products') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('picklist') }}">{{ __('Picklist') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('packlist') }}">{{ __('Packlist') }}</a>
                        </li>

                        <li class="nav-item dropdown">
                            <!-- User dropdown menu -->
                            <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ __('Reports') }}<span class="caret"></span>
                            </a>

                            <!-- Menu Items END -->
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('picks_report') }}">{{ __('Picks') }}</a>
                                <a class="dropdown-item" href="{{ route('orders') }}">{{ __('Orders') }}</a>
                            </div>
                        </li>
                    </ul>


                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">

                            <li class="nav-item dropdown">
                                <!-- User dropdown menu -->
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} (id: {{ Auth::user()->id }})<span class="caret"></span>
                                </a>

                                <!-- Menu Items END -->
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('picklistOld') }}">
                                        {{ __('Old Picklist') }}
                                    </a>

                                    <!-- Settings -->
                                    <a class="dropdown-item" href="{{ route('settings') }}">
                                        {{ __('Settings') }}
                                    </a>

                                    <!-- users -->
                                    @can('manage users')
                                        <a class="dropdown-item" href="{{ route('users') }}">
                                            {{ __('Users') }}
                                        </a>
                                    @endcan

                                    <!-- Logout -->
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>

                                </div>
                            </li>

                    </ul>
                </div>

            </div>
        </nav>
        @endauth

        <main class="py-4 pl-1 pr-2">
            @yield('content')
        </main>

        <vue-snotify></vue-snotify>
    </div>
</body>
</html>
