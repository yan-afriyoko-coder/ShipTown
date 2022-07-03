<?php

namespace App\Modules\Rmsapi\src\Listeners;

class Every10minEventListener
{
    public function handle()
    {
        ProcessProductImports::dispatch();
    }
}
