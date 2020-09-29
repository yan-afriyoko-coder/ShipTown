<?php

namespace Tests\Unit\Services;

use App\Services\AutoPilot;
use Tests\TestCase;

class AutoPilotTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertEquals(
            50,
            AutoPilot::getAutoPilotPackingDailyMax()
        );
    }
}
