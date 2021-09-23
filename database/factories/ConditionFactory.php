<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Faker\Generator as Faker;

$factory->define(Condition::class, function (Faker $faker) {
    return [
        'automation_id' => factory(Automation::class)->create()->getKey(),
    ];
});
