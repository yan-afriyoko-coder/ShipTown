<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;

trait LogsActivityTrait
{
    use LogsActivity;

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function log($message)
    {
        activity()->on($this)->log($message);

        return $this;
    }
}
