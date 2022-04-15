<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MailTemplate;
use Faker\Generator as Faker;

$factory->define(MailTemplate::class, function (Faker $faker) {
    return [
        'mailable' => \App\Mail\OrderMail::class,
        'html_template' => $faker->randomHtml(),
    ];
});
