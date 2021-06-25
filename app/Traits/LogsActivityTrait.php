<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;

trait LogsActivityTrait
{
    use LogsActivity;

    public function log($message)
    {
        activity()->on($this)->log($message);

        return $this;
    }
}
