<?php

namespace App\Jobs;

use http\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class JobImportOrderApi2Cart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $store_key = "";

        $guzzle = new \GuzzleHttp\Client([
            'base_uri' =>  'https://api.api2cart.com/v1.1/',
            'timeout' => 60,
            'exceptions' => true,
        ]);

        $result = $guzzle->get(
            'orders.list.json',
            [
                'query' => [
                    "api_key" => env('API2CART_API_KEY', ""),
                    "store_key" => $store_key
                ]
            ]
        );
    }
}
