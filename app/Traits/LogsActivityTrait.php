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
        $activity = activity()->on($this);
        $activity->log($message);

        \Log::debug('Activity', ['message' => $message, 'id' => $this->getKey(), 'class' => get_class($this)]);
        return $this;
    }
}
