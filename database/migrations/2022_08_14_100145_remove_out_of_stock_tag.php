<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Tags\Tag;

class RemoveOutOfStockTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tag = Tag::findFromString('Out Of Stock');

        if ($tag) {
            DB::table('taggables')->where('tag_id', $tag->getKey())->delete();
            DB::table('tags')->where('id', $tag->getKey())->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
