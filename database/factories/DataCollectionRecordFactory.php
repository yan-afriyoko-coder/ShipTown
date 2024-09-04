<?php

namespace Database\Factories;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataCollectionRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'data_collection_id' => function () {
                return DataCollection::factory()->create();
            },
            'product_id' => function () {
                return Product::factory()->create();
            },
            'quantity_requested' => rand(1, 100),
            'quantity_scanned' => rand(1, 100),
            'unit_cost' => $this->faker->randomFloat(3, 0, 100),
            'unit_sold_price' => $this->faker->randomFloat(3, 0, 100),
            'unit_full_price' => $this->faker->randomFloat(3, 0, 100),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (DataCollectionRecord $dataCollectionRecord) {
            $inventory = Inventory::query()->where([
                'product_id' => $dataCollectionRecord->product_id,
                'warehouse_id' => $dataCollectionRecord->dataCollection->warehouse_id,
            ])
                ->first();
            $dataCollectionRecord->inventory_id = $inventory->id;
            $pricing = ProductPrice::query()->where([
                'product_id' => $dataCollectionRecord->product_id,
                'warehouse_id' => $dataCollectionRecord->dataCollection->warehouse_id,
            ])
                ->first();
            $dataCollectionRecord->unit_full_price = $pricing->price;
            $dataCollectionRecord->unit_sold_price = $pricing->current_price;
            $dataCollectionRecord->unit_cost = $pricing->cost;
        });
    }
}
