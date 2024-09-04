<?php

namespace Database\Seeders;

use App\Models\ProductAlias;
use Illuminate\Database\Seeder;

class ProductAliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductAlias::factory()->count(10)->create();

        $this->createAliases([
            '45',
            '55',
            '65',
            '3276000690573',
        ]);
    }

    private function createAliases(array $aliasList): void
    {
        foreach ($aliasList as $alias) {
            $aliasExists = ProductAlias::query()->where(['alias' => $alias])->exists();

            if (! $aliasExists) {
                ProductAlias::factory()
                    ->create(['alias' => $alias]);
            }
        }
    }
}
