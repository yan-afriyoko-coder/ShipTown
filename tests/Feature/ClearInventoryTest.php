<?php

namespace Tests\Feature;

use App\Models\Inventory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClearInventoryTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Inventory::query()->where('location_id','=',1)->delete();
        dd(Inventory::query()->where('location_id','=',1)->limit(10)->get());
    }
}
