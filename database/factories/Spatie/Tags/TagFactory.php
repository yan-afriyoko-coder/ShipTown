<?php



namespace Database\Factories\Spatie\Tags;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Tags\Tag;

class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
