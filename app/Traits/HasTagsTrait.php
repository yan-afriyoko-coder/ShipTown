<?php

namespace App\Traits;

use App\Events\Product\TagAttachedEvent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

trait HasTagsTrait
{
    use HasTags {
        attachTag as traitHasTagsAttachTag;
    }

    /**
     * @param string|Tag $tag
     *
     * @param string|null $type
     * @return $this
     */
    public function attachTag($tag, string $type = null)
    {
        try {
            $this->traitHasTagsAttachTag($tag, $type);
            TagAttachedEvent::dispatch($this, $tag);
        } catch (Exception $exception) {
            $this->log("Could not assign '{$tag}'' tag");
        }

        return $this;
    }


    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|\ArrayAccess|\Spatie\Tags\Tag $tags
     *
     * @param string|null $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutAllTags(Builder $query, $tags, string $type = null): Builder
    {
        $tags = static::convertToTags($tags, $type);

        collect($tags)->each(function ($tag) use ($query) {
            $query->whereDoesntHave('tags', function (Builder $query) use ($tag) {
                $query->where('tags.id', $tag ? $tag->id : 0);
            });
        });

        return $query;
    }
}
