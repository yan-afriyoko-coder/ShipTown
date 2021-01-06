<?php

namespace App\Traits;

use App\Events\Product\TagAttachedEvent;
use ArrayAccess;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use phpDocumentor\Reflection\Types\Mixed_;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

trait HasTagsTrait
{
    use HasTags {
        attachTag as traitHasTagsAttachTag;
    }

    /**
     * @param string|Tag $tag
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
     * @param Builder $query
     * @param array|ArrayAccess|Tag $tags
     *
     * @param string|null $type
     * @return Builder
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

    /**
     * @param array|null $tags
     * @return bool
     */
    public function hasTags(array $tags = null): bool
    {
        return static::withAllTags($tags)->whereId($this->getKey())->exists();
    }

    /**
     * @param array|null $tags
     * @return bool
     */
    public function doesNotHaveTags(array $tags = null): bool
    {
        return !$this->hasTags($tags);
    }
}
