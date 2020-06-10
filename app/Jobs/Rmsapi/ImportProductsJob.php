<?php

namespace App\Jobs\Rmsapi;

use App\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Client as RmsapiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportProductsJob implements ShouldQueue
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
        $connections = RmsapiConnection::all();

        foreach ($connections as $connection) {
           $products = RmsapiClient::GET($connection, 'api/products');
        }
    }
}
