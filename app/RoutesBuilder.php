<?php

namespace App;

use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RoutesBuilder
{
    public static function apiResource(string $name): PendingResourceRegistration
    {
        $nameParts = collect(explode('/', $name))->prepend('api');
        $controllerName = $nameParts->pop();

        $controllerName = Str::ucfirst(Str::singular($controllerName));
        $controllerPath = Str::title(implode('\\', $nameParts->toArray()));
        $controllerClass = $controllerPath. '\\' . $controllerName . 'Controller';

        $routeName = implode('.', $nameParts->toArray());

        return Route::apiResource(
            $name,
            $controllerClass,
            ['as' => $routeName]
        );
    }
}
