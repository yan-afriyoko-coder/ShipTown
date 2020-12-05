<?php

namespace App\Traits;

use App\Events\Product\TagAttachedEvent;
use Exception;
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
}
