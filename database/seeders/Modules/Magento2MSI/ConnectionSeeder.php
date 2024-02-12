<?php

namespace Database\Seeders\Modules\Magento2MSI;

use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use Illuminate\Database\Seeder;

class ConnectionSeeder extends Seeder
{
    public function run()
    {
        if (env('TEST_MODULES_MAGENTO2MSI_BASE_URL') === null) {
            return;
        }

        Magento2msiConnection::query()->updateOrCreate([
            'base_url' => env('TEST_MODULES_MAGENTO2MSI_BASE_URL'),
        ], [
            'api_access_token' => env('TEST_MODULES_MAGENTO2MSI_ACCESS_TOKEN'),
        ]);
    }
}
