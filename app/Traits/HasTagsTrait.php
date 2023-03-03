<?php

namespace App\Traits;

use ArrayAccess;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

/**
 * @method static withAllTags(string[] $array)
 */
trait HasTagsTrait
{
    use HasTags {
        attachTags as originalAttachTags;
        detachTags as originalDetachTags;
        scopeWithAllTags as traitHasTagsScopeWithAllTags;
    }

    /**
     * @param $tag
     */
    protected function onTagAttached($tag)
    {
        // override this function on model
    }

    /**
     * @param $tag
     */
    protected function onTagDetached($tag)
    {
        // override this function on model
    }

    /**
     * @param array|null $tags
     *
     * @return bool
     */
    public function hasTags(array $tags = null): bool
    {
        return static::withAllTags($tags)->whereId($this->getKey())->exists();
    }

    public function scopeHasTags(Builder $query, $tags): Builder
    {
        $tags = collect(func_get_args());
        $tags->shift();

        $toArray = $tags->toArray();

        return $this->traitHasTagsScopeWithAllTags($query, $toArray);
    }

    /**
     * @param array $tags
     * @param string|null $type
     * @return $this
     * @throws Exception
     */
    public function attachTags($tags, string $type = null): self
    {
        collect($tags)
            ->filter()
            ->each(function ($tag) use ($type) {
                retry(3, function () use ($tag, $type) {
                    if ($this->hasTags([$tag])) {
                        return;
                    }
                    $this->originalAttachTags([$tag], $type);

                    // if LogsActivityTrait is used, we log it
                    if (in_array(LogsActivityTrait::class, class_uses_recursive($this))) {
                        $this->log('attached "'.$tag.'" tag');
                    }

                    $this->onTagAttached($tag);
                }, 1);
            });

        return $this;
    }

    /**
     * @param array $tags
     * @param string|null $type
     * @return $this
     */
    public function detachTags($tags, string $type = null): self
    {
        collect($tags)
            ->filter()
            ->each(function ($tag) use ($type) {
                if ($this->doesNotHaveTags([$tag])) {
                    return;
                }
                $this->originalDetachTags([$tag], $type);
                $this->onTagDetached($tag);
                $this->log('detached "'.$tag.'" tag');
            });

        return $this;
    }

    /**
     * @param string|Tag  $tag
     * @param string|null $type
     *
     * @return $this
     */
    public function detachTagSilently($tag, string $type = null): self
    {
        activity()->withoutLogs(function () use ($tag, $type) {
            $this->detachTag($tag, $type);
        });

        return $this;
    }

    /**
     * @param Builder               $query
     * @param array|ArrayAccess|Tag $tags
     * @param string|null           $type
     *
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
     *
     * @return bool
     */
    public function doesNotHaveTags(array $tags = null): bool
    {
        return !$this->hasTags($tags);
    }


    public function syncTagByType(string $tagType, string $tagName)
    {
        $tag = $this->tags()->where(['type' => $tagType])->first();

        if ($tag) {
            $this->detachTag($tag);
        }

        $this->attachTag($tagName, $tagType);
    }
}
