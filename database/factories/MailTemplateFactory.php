<?php

namespace Database\Factories;

use App\Mail\OrderMail;
use App\Models\MailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailTemplateFactory extends Factory
{
    protected $model = MailTemplate::class;

    public function definition(): array
    {
        return [
            'mailable' => OrderMail::class,
            'html_template' => $this->faker->randomHtml(),
        ];
    }
}
