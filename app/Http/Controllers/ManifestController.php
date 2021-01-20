<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

class ManifestController extends Controller
{
    public function index()
    {
        return json_encode([
            "name" => " Product Management ". Str::ucfirst(config('app.tenant_name')),
            "short_name" => "PM " . Str::ucfirst(config('app.tenant_name')),
            "icons" => [
                [
                    "src" => "/img/icons/android-chrome-192x192.png",
                    "sizes" => "192x192",
                    "type" => "image/png"
                ],
                [
                    "src" => "/img/icons/android-chrome-512x512.png",
                    "sizes" => "512x512",
                    "type" => "image/png"
                ]
            ],
            "start_url" => "/index.php",
            "display" => "standalone",
            "background_color" => "#3E4EB8",
            "theme_color" => "#2F3BA2"
        ]);
    }
}
