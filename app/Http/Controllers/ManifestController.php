<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Class ManifestController.
 */
class ManifestController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'name'       => ' Product Management '.Str::ucfirst(config('app.tenant_name')),
            'short_name' => 'PM '.Str::ucfirst(config('app.tenant_name')),
            'icons'      => [
                [
                    'src'   => '/img/icons/android-chrome-192x192.png',
                    'sizes' => '192x192',
                    'type'  => 'image/png',
                ],
                [
                    'src'   => '/img/icons/android-chrome-512x512.png',
                    'sizes' => '512x512',
                    'type'  => 'image/png',
                ],
            ],
            'start_url'        => '/index.php',
            'display'          => 'standalone',
            'background_color' => '#3E4EB8',
            'theme_color'      => '#2F3BA2',
        ]);
    }
}
