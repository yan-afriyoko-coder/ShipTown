<?php

namespace Tests;

use App;

trait ResetsDatabase
{
    protected function setUp(): void
    {
        parent::setUp();

        ray()->showApp();

        ray()->clearAll();
        ray()->className($this)->blue();

        App\Console\Commands\ClearDatabaseCommand::resetDatabase();
    }
}
