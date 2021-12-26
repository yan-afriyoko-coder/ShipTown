<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Modules\DpdIreland\src\Models\DpdIreland::class, function (Faker $faker) {
    return [
        'live'              => false,
        'token'             => env('TEST_DPD_TOKEN', $faker->randomNumber(9)),
        'user'              => env('TEST_DPD_USER', $faker->randomNumber(6)),
        'password'          => env('TEST_DPD_PASSWORD', $faker->randomNumber(6)),
        'contact'           => 'John Smith',
        'contact_telephone' => '12345678901',
        'contact_email'     => 'john.smith@dpd.ie',
        'business_name'     => 'JS Business',
        'address_line_1'    => 'DPD Ireland, Westmeath',
        'address_line_2'    => 'Unit 2B Midland Gateway Bus',
        'address_line_3'    => 'Kilbeggan',
        'address_line_4'    => 'Westmeath',
        'country_code'      => 'IE',
    ];
});
