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
        Tag::query()->firstOrCreate(['name' => 'slow']);
        Tag::query()->firstOrCreate(['name' => 'discontinued']);
        Tag::query()->firstOrCreate(['name' => 'best seller']);

        Tag::all()->each(function ($tag) {
            Product::query()->inRandomOrder()->first()->attachTag($tag);
        });
    }
}
