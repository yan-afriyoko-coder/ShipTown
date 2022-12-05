<?php



namespace Database\Factories\Modules\Automations\src\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;

class ConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'automation_id' => Automation::factory()->create()->getKey(),
        ];
    }
}
