<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;

trait LogsActivityTrait
{
    use LogsActivity;

    protected static bool $logOnlyDirty = true;
    protected static bool $submitEmptyLogs = false;

    public function log($message): self
    {
        activity()->on($this)->log($message);

        return $this;
    }
}
