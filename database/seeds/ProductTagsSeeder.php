<?php

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
        Product::query()->inRandomOrder()->first()->attachTag('oversold');
        // factory(Tag::class, Product::query()->count())
        //     ->create()
        //     ->each(function ($tag) {
        //         Product::query()->inRandomOrder()->first()->attachTag($tag);
        //     });
    }
}
