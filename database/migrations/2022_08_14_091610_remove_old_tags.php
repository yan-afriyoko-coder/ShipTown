<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Tags\Tag;

class RemoveOldTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tag = Tag::findFromString('awaiting_webhook_publish');
        DB::table('taggables')->where('tag_id', $tag->getKey())->delete();
    }
}
