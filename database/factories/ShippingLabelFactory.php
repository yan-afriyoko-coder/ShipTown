<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\ShippingLabel;
use App\User;

class ShippingLabelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $shipping_number = $this->faker->toUpper($this->faker->randomLetter.$this->faker->randomLetter.'100'.$this->faker->randomNumber(8));

        $order = Order::query()->inRandomOrder()->first() ?? Order::factory()->create();
        $user = User::query()->inRandomOrder()->first() ?? User::factory()->create();

        return [
            'order_id'        => $order->getKey(),
            'carrier'         => $this->faker->randomElement(['DPD', 'UPS', 'SEUR', 'DHL', 'MRW', 'DPD Ireland', 'DPD UK']),
            'shipping_number' => $shipping_number,
            'tracking_url'    => $this->faker->url,
            'user_id'         => $user->getKey(),
        ];
    }
}
