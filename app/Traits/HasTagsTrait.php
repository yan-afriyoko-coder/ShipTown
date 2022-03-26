<?php

namespace App\Traits;

use ArrayAccess;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

trait HasTagsTrait
{
    use HasTags {
        attachTag as traitHasTagsAttachTag;
        detachTag as traitHasTagsDetachTag;
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
     * @param string|Tag  $tag
     * @param string|null $type
     *
     * @return self
     */
    public function attachTag($tag, string $type = null): self
    {
        try {
            if ($this->hasTags([$tag])) {
                return $this;
            }

            $this->traitHasTagsAttachTag($tag, $type);
            $this->onTagAttached($tag);
            $this->log('"'.$tag.'" tag attached');
        } catch (Exception $exception) {
            $this->log("Could not attach '{$tag}'' tag");
        }

        return $this;
    }

    /**
     * @param string|Tag  $tag
     * @param string|null $type
     *
     * @return $this
     */
    public function detachTag($tag, string $type = null): self
    {
        try {
            if ($this->doesNotHaveTags([$tag])) {
                return $this;
            }

            $this->traitHasTagsDetachTag($tag, $type);
            $this->onTagDetached($tag);
            $this->log('"'.$tag.'" tag detached');
        } catch (Exception $exception) {
            $this->log("Could not detach '{$tag}'' tag");
        }

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
}
