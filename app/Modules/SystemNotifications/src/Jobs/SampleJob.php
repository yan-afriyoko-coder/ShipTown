<?php

namespace App\Modules\Notifications\src\Jobs;

use App\Abstracts\UniqueJob;

class SampleJob extends UniqueJob
{
    public function handle(): bool
    {
        //
        return true;
    }
}
