<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Spatie\Tags\Tag;

class ProductTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::factory()->create(['name' => 'slow']);
        Tag::factory()->create(['name' => 'discontinued']);
        Tag::factory()->create(['name' => 'best seller']);

        Tag::all()->each(function ($tag) {
            Product::query()->inRandomOrder()->first()->attachTag($tag);
        });
    }
}
