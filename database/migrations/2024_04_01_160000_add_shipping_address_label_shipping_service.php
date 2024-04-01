<?php

use App\Models\ShippingService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Modules\AddressLabel\src\Services;

return new class extends Migration
{
    public function up(): void
    {
        ShippingService::query()
            ->updateOrCreate([
                'code' => 'billing_address_label',
            ], [
                'service_provider_class' => Services\BillingAddressLabelShippingService::class,
            ]);
    }
};
