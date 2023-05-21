<?php

use App\Modules\MagentoApi\src\Models\MagentoConnection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        MagentoConnection::query()->whereNull('access_token_encrypted')
            ->each(function (MagentoConnection $connection) {
                $connection->api_access_token = config('magento.token');
                $connection->save();
            });
    }
};
