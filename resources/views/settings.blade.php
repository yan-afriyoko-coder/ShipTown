@extends('layouts.app')

@section('title', __('Settings'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="list-group">
                <a href="{{ route('settings.general') }}" class="list-group-item list-group-item-action">
                    <div class="media">
                        <div class="p-3 rounded mr-3 bg-light">
                            <font-awesome-icon icon="cog" class="fa-lg align-self-center"></font-awesome-icon>
                        </div>
                        <div class="media-body">
                            <div class="h5 text-primary font-weight-bolder">General</div>
                            <div>View and update general application settings</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('users') }}" class="list-group-item list-group-item-action">
                    <div class="media">
                        <div class="p-3 rounded mr-3 bg-light">
                            <font-awesome-icon icon="user-edit" class="fa-lg align-self-center"></font-awesome-icon>
                        </div>
                        <div class="media-body">
                            <div class="h5 text-primary font-weight-bolder">Users</div>
                            <div class="text-secondary">Manage Users</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('settings.printnode') }}" class="list-group-item list-group-item-action">
                    <div class="media">
                        <div class="p-3 rounded mr-3 bg-light">
                            <font-awesome-icon icon="print" class="fa-lg align-self-center"></font-awesome-icon>
                        </div>
                        <div class="media-body">
                            <div class="h5 text-primary font-weight-bolder">PrintNode</div>
                            <div class="text-secondary">View and update PrintNode integration  settings</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('settings.rmsapi') }}" class="list-group-item list-group-item-action">
                    <div class="media">
                        <div class="p-3 rounded mr-3 bg-light">
                            <font-awesome-icon icon="code" class="fa-lg align-self-center"></font-awesome-icon>
                        </div>
                        <div class="media-body">
                            <div class="h5 text-primary font-weight-bolder">Microsoft Dynamic RMS 2.0 API</div>
                            <div class="text-secondary">View and update RMS API integration settings</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('settings.dpd-ireland') }}" class="list-group-item list-group-item-action">
                    <div class="media">
                        <div class="p-3 rounded mr-3 bg-light">
                            <font-awesome-icon icon="truck" class="fa-lg align-self-center"></font-awesome-icon>
                        </div>
                        <div class="media-body">
                            <div class="h5 text-primary font-weight-bolder">DPD Ireland API</div>
                            <div class="text-secondary">View and update DPD Ireland integration settings</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('settings.api2cart') }}" class="list-group-item list-group-item-action">
                    <div class="media">
                        <div class="p-3 rounded mr-3 bg-light">
                            <font-awesome-icon icon="shopping-cart" class="fa-lg align-self-center"></font-awesome-icon>
                        </div>
                        <div class="media-body">
                            <div class="h5 text-primary font-weight-bolder">Api2cart</div>
                            <div class="text-secondary">View and update Api2cart integration settings</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('settings.api') }}" class="list-group-item list-group-item-action">
                    <div class="media">
                        <div class="p-3 rounded mr-3 bg-light">
                            <font-awesome-icon icon="key" class="fa-lg align-self-center"></font-awesome-icon>
                        </div>
                        <div class="media-body">
                            <div class="h5 text-primary font-weight-bolder">API</div>
                            <div class="text-secondary">View and update application API settings and tokens</div>
                        </div>
                    </div>
                </a>

                <a href="{{ url('admin/tools/queue-monitor') }}" target="_blank" class="list-group-item list-group-item-action">
                    <div class="media">
                        <div class="p-3 rounded mr-3 bg-light">
                            <font-awesome-icon icon="desktop" class="fa-lg align-self-center"></font-awesome-icon>
                        </div>
                        <div class="media-body">
                            <div class="h5 text-primary font-weight-bolder">Queue Monitor</div>
                            <div class="text-secondary">Open jobs monitor</div>
                        </div>
                    </div>
                </a>

                <a href="{{ url('admin/tools/log-viewer') }}" target="_blank" class="list-group-item list-group-item-action">
                    <div class="media">
                        <div class="p-3 rounded mr-3 bg-light">
                            <font-awesome-icon icon="clipboard-list" class="fa-lg align-self-center"></font-awesome-icon>
                        </div>
                        <div class="media-body">
                            <div class="h5 text-primary font-weight-bolder">Log Viewer</div>
                            <div class="text-secondary">View application logs</div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
</div>
@endsection
