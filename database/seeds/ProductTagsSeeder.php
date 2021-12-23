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
        factory(Tag::class)->create(['name' => 'slow']);
        factory(Tag::class)->create(['name' => 'discontinued']);
        factory(Tag::class)->create(['name' => 'best seller']);

        Tag::all()->each(function ($tag) {
            Product::query()->inRandomOrder()->first()->attachTag($tag);
        });
    }
}
