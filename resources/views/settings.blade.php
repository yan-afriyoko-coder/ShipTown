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
                <a href="{{ route('settings.general') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">General</div>
                        <div>View and setting-desc application settings</div>
                    </div>
                </a>

                <a href="{{ route('users') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="user-edit" class="fa-lg"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Users</div>
                        <div class="setting-desc">Manage Users</div>
                    </div>
                </a>

                <a href="{{ route('settings.printnode') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="print" class="fa-lg"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">PrintNode</div>
                        <div class="setting-desc">View and update PrintNode integration  settings</div>
                    </div>
                </a>

                <a href="{{ route('settings.rmsapi') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="code" class="fa-lg"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Microsoft Dynamic RMS 2.0 API</div>
                        <div class="setting-desc">View and update RMS API integration settings</div>
                    </div>
                </a>

                <a href="{{ route('settings.dpd-ireland') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="truck" class="fa-lg"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">DPD Ireland API</div>
                        <div class="setting-desc">View and update DPD Ireland integration settings</div>
                    </div>
                </a>

                <a href="{{ route('settings.api2cart') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="shopping-cart" class="fa-lg"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Api2cart</div>
                        <div class="setting-desc">View and update Api2cart integration settings</div>
                    </div>
                </a>

                <a href="{{ route('settings.api') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="key" class="fa-lg"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">API</div>
                        <div class="setting-desc">View and update application API settings and tokens</div>
                    </div>
                </a>

                <a href="{{ url('admin/tools/queue-monitor') }}" target="_blank" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="desktop" class="fa-lg"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Queue Monitor</div>
                        <div class="setting-desc">Open jobs monitor</div>
                    </div>
                </a>

                <a href="{{ url('admin/tools/log-viewer') }}" target="_blank" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="clipboard-list" class="fa-lg"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Log Viewer</div>
                        <div class="setting-desc">View application logs</div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

@section('css')
    <style>
        .setting-list{
            padding: 0.75rem 1.25rem;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.125);
            width: 100%;
            color: #495057;
            display: flex;
            align-items: flex-start;
        }

        .setting-list:hover, .setting-list:focus {
            color: #495057;
            text-decoration: none;
            background-color: #f8f9fa;
        }

        .setting-icon{
            padding: 1rem;
            margin-right: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }

        .setting-icon:hover{
            background-color: unset;
        }

        .setting-title{
            color: #3490dc;
            font-weight: bolder;
            font-size: 1.125rem;
            line-height: 1.2;
            margin-bottom: 0.5rem;
        }

        .setting-desc{
            color: #6c757d;
        }
    </style>
@endsection
