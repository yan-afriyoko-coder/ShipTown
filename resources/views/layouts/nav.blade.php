@auth
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-none d-md-block" href="{{ url('/') }}">
                PM
                @yield('title')
            </a>

            <div class="d-block d-md-none">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a type="button" class="btn btn-secondary" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    <a type="button" class="btn btn-secondary" href="{{ route('products') }}">{{ __('Products') }}</a>
                    <a type="button" class="btn btn-secondary" href="{{ route('orders') }}">{{ __('Orders') }}</a>
                </div>
            </div>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
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

                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Picklist') }}<span class="caret"></span>
                        </a>

                        <!-- Menu Items END -->
                        <div class="dropdown-menu dropdown-menu-right text-center text-md-left" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('picklist') . '?order.status_code=picking&inventory_source_location_id=100'}}">
                                {{ __('Web: picking') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('picklist', ['order.status_code' => 'paid,picking', 'inventory_source_location_id' => '99', 'created_between' => '-1year,-1hour']) }}">
                                {{ __('Warehouse: paid,picking') }}
                            </a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Packlist') }}<span class="caret"></span>
                        </a>

                        <!-- Menu Items END -->
                        <div class="dropdown-menu dropdown-menu-left text-center text-md-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('autopilot.packlist', ['inventory_source_location_id' => 100, 'status' => 'packing_web', 'is_picked' => 'true', 'sort' => 'order_placed_at,product_line_count,total_quantity_ordered']) }}">
                                {{ __('Status: packing_web') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('autopilot.packlist', ['inventory_source_location_id' => 99, 'status' => 'packing_warehouse', 'sort' => 'order_placed_at']) }}">
                                {{ __('Status: packing_warehouse') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('autopilot.packlist',  ['inventory_source_location_id' => 100, 'status' => 'single_line_orders', 'sort' => 'min_shelf_location']) }}">
                                {{ __('Status: single_line_orders') }}
                            </a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Reports') }}<span class="caret"></span>
                        </a>

                        <!-- Menu Items END -->
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('performance.dashboard') .'?between_dates=-7days,now' }}">{{ __('Performance Dashboard') }}</a>
                            <a class="dropdown-item" href="{{ route('reports.picks') }}">{{ __('Picks') }}</a>
                            <a class="dropdown-item" href="{{ route('reports.shipments') }}">{{ __('Shipments') }}</a>
                            <a class="dropdown-item" href="{{ route('ready_order_shipments_as_csv') }}" target="_blank">{{ __("Download Ready Shipments") }}</a>
                            <a class="dropdown-item" href="{{ route('partial_order_shipments_as_csv') }}" target="_blank">{{ __("Download Today's Partial Shipments") }}</a>
                            <a class="dropdown-item" href="{{ route('warehouse_picks.csv').'?filter[user_id]=8&filter[pick.created_between]=today,today' }}" target="_blank">{{ __("Download Today's Warehouse Picks") }}</a>
                            <a class="dropdown-item" href="{{ route('warehouse_shipped.csv').'?filter[packer_user_id]=8&filter[order.packed_between]=today,today' }}" target="_blank">{{ __("Download Today's Warehouse Shipped") }}</a>
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
