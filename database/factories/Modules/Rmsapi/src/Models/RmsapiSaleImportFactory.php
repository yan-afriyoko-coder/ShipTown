<?php

namespace Database\Factories\Modules\Rmsapi\src\Models;

use App\Models\Product;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use Illuminate\Database\Eloquent\Factories\Factory;

class RmsapiSaleImportFactory extends Factory
{
    protected $model = RmsapiSaleImport::class;

    public function definition(): array
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var RmsapiConnection $rmsapiConnection */
        $rmsapiConnection = RmsapiConnection::factory()->create();

        return [
            'connection_id' => $rmsapiConnection->id,
            'sku' => $product->sku,
            'quantity' => $this->faker->numberBetween(1, 100),
            'transaction_number' => $this->faker->numberBetween(100000, 200000),
            'transaction_entry_id' => $this->faker->numberBetween(700000, 900000),
            'comment' => ''
        ];
    }
}
