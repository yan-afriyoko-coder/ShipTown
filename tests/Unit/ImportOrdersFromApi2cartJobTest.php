<?php

namespace Tests\Unit;

use App\Jobs\ImportOrdersFromApi2cartJob;
use App\User;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportOrdersFromApi2cartJobTest extends TestCase
{
    public function test_if_job_runs_without_exceptions()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $user_id = auth()->id();

        $job = new ImportOrdersFromApi2cartJob($user_id);

        $job->handle();

        $this->assertTrue(true);
    }
}
