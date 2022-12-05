<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MailTemplate;

class MailTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'mailable' => \App\Mail\OrderMail::class,
            'html_template' => $this->faker->randomHtml(),
        ];
    }
}
