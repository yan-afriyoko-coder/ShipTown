<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
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
        $config = Configuration::first();

        return response()->json([
            'name'       => 'Product Management ' . $config->business_name,
            'short_name' => 'PM ' . $config->business_name,
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
            'background_color' => '#007bff',
            'theme_color'      => '#007bff',
        ]);
    }
}
