<?php

namespace App\Providers;

use App\Models\NavigationMenu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.nav', function ($view) {
            $navigationMenu = NavigationMenu::all();
            $navigationMenuPicklist = $navigationMenu->where('group', 'picklist');
            $navigationMenuPacklist = $navigationMenu->where('group', 'packlist');
            $navigationMenuReports  = $navigationMenu->where('group', 'reports');
            $view->with(compact('navigationMenuPicklist', 'navigationMenuPacklist', 'navigationMenuReports'));
        });
    }
}
