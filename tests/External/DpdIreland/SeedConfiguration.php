<?php

namespace Tests\External\DpdIreland;

use App\Modules\DpdIreland\src\Models\DpdIreland;

trait SeedConfiguration
{
    /**
     * Delete previous DpdIreland configuration and create a new one.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        DpdIreland::query()->delete();
        DpdIreland::query()->firstOrCreate([
            'live'              => false,
            'user'              => env('TEST_DPD_USER'),
            'password'          => env('TEST_DPD_PASSWORD'),
            'token'             => env('TEST_DPD_TOKEN'),
            'contact'           => 'DPD Contact',
            'contact_telephone' => '0860000000',
            'contact_email'     => 'testemail@dpd.ie',
            'business_name'     => 'DPD API Test Limited',
            'address_line_1'    => 'Athlone Business Park',
            'address_line_2'    => 'Dublin Road',
            'address_line_3'    => 'Athlone',
            'address_line_4'    => 'Co. Westmeath',
            'country_code'      => 'IE',
        ]);
    }
}
