<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\OrderAddress;
use Faker\Generator as Faker;

$factory->define(OrderAddress::class, function (Faker $faker) {
    return [
        'company'       => $faker->company,
        'gender'        => $faker->title,
        'first_name'    => $faker->firstName,
        'last_name'     => $faker->lastName,
        'address1'      => $faker->address,
        'address2'      => $faker->address,
        'postcode'      => $faker->postcode,
        'city'          => $faker->city,
        'state_code'    => $faker->countryCode,
        'state_name'    => $faker->state,
        'country_code'  => $faker->countryCode,
        'country_name'  => $faker->country,
        'phone'         => $faker->phoneNumber,
        'fax'           => $faker->phoneNumber,
        'website'       => $faker->url,
        'region'        => $faker->word,
    ];
});
