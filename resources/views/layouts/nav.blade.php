@auth
{{--    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm mb-0">--}}
    <nav class="navbar navbar-expand-md navbar-light bg-secondary mb-2 p-0 bg-white">
        <div class="container text-white">
            <a class="navbar-brand d-none d-md-block" href="{{ url('/') }}">
                PM
                @yield('title')
            </a>

            <div class="d-block d-md-none mb-0 navbar-dark w-100 bg-secondary">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a type="button" class="btn btn-secondary bg-secondary" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    <a type="button" class="btn btn-secondary bg-secondary " href="{{ route('products') }}">{{ __('Products') }}</a>
                    <a type="button" class="btn btn-secondary bg-secondary" href="{{ route('orders') }}">{{ __('Orders') }}</a>
                </div>
                <button class="navbar-toggler navbar-light float-right border-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon text-white"></span>
                </button>
            </div>

            <style>
                .collapsing {
                    -webkit-transition: none;
                    transition: none;
                    display: none;
                }
            </style>
            <div class="collapse navbar-collapse bg-white" id="navbarSupportedContent" style="transition: none !important;">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto text-center text-md-left">
                    <li class="nav-item d-none d-md-inline d-md-block">
                        <a class="nav-link" href="{{ route('dashboard') }}">|</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link" href="{{ route('products') }}">{{ __('Products') }}</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link" href="{{ route('orders') }}">{{ __('Orders') }}</a>
                    </li>

                    <!-- Picklist Dropdown -->
                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Picklist') }}<span class="caret"></span>
                        </a>

                        <!-- Menu Items END -->
                        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
                           @foreach ($navigationMenuPicklist as $menu)
                                <a class="dropdown-item" href="{{ $menu->url }}">
                                    {{ $menu->name }}
                                </a>
                            @endforeach
                        </div>
                    </li>

                    <!-- Packlist Dropdown -->
                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Packlist') }}<span class="caret"></span>
                        </a>

                        <!-- Packlist Menu -->
                        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
                            @foreach ($navigationMenuPacklist as $menu)
                                <a class="dropdown-item" href="{{ $menu->url }}">
                                    {{ $menu->name }}
                                </a>
                            @endforeach
                        </div>
                    </li>

                    <!-- Reports Dropdown -->
                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Reports') }}<span class="caret"></span>
                        </a>

                        <!-- Menu Items END -->
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('performance.dashboard') .'?between_dates=-7days,now' }}">{{ __('Performance Dashboard') }}</a>
                            <a class="dropdown-item" href="{{ route('reports.picks') }}">{{ __('Picks') }}</a>
                            <a class="dropdown-item" href="{{ route('reports.shipments') }}">{{ __('Shipments') }}</a>
                            <a class="dropdown-item" href="{{ route('ready_order_shipments_as_csv').'?include=order&filter[shipping_number]=LJ&filter[order.status_code]=ready&fields=shipping_number,order.order_number&filename=AnPost_ready_shipments.csv'}}" target="_blank">{{ __("Download AnPost Shipments") }}</a>
                            <a class="dropdown-item" href="{{ route('ready_order_shipments_as_csv').'?include=order&filter[shipping_number]=6127&filter[order.status_code]=ready&fields=id,shipping_number,order.order_number&filename=dpd_ready_shipments.csv'}}" target="_blank">{{ __("Download DPD Shipments") }}</a>
                            <a class="dropdown-item" href="{{ route('partial_order_shipments_as_csv') }}" target="_blank">{{ __("Download Today's Partial Shipments") }}</a>
                            <a class="dropdown-item" href="{{ route('warehouse_picks.csv').'?filter[user_id]=8&filter[created_between]=today,now' }}" target="_blank">{{ __("Download Today's Warehouse Picks") }}</a>
                            <a class="dropdown-item" href="{{ route('warehouse_shipped.csv').'?filter[packer_user_id]=8&filter[order.packed_between]=today,now' }}" target="_blank">{{ __("Download Today's Warehouse Shipped") }}</a>
                            @foreach ($navigationMenuReports as $menu)
                                <a class="dropdown-item" href="{{ $menu->url }}">
                                    {{ $menu->name }}
                                </a>
                            @endforeach

                        </div>
                    </li>
                </ul>


                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto text-center text-md-left">

                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} (id: {{ Auth::user()->id }})<span class="caret"></span>
                        </a>

                        <!-- Menu Items END -->
                        <div class="dropdown-menu dropdown-menu-right text-center text-md-left" aria-labelledby="navbarDropdown">
                            {{-- Profile --}}
                            <a class="dropdown-item" href="{{ route('setting-profile') }}">
                                {{ __('Profile') }}
                            </a>

                            <!-- Settings -->
                            @hasrole('admin')
                                <a class="dropdown-item" href="{{ route('settings') }}">
                                    {{ __('Settings') }}
                                </a>
                            @endhasrole

                            <a class="dropdown-item" href="https://www.youtube.com/channel/UCl04S5dRXop1ZdZsOqY3OnA" target="_blank">
                                {{ __('YouTube') }}
                            </a>

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
