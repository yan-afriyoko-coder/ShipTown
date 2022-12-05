<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatus;
use App\User;
use Carbon\Exceptions\InvalidFormatException;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $shippingAddress = OrderAddress::factory()->create();

        /** @var OrderStatus $orderStatus */
        $orderStatus = OrderStatus::query()->inRandomOrder()->first() ?? OrderStatus::factory()->create();

        do {
            try {
                $dateTime = $this->faker->dateTimeBetween('-7days', now());
                \Carbon\Carbon::parse($dateTime, new \DateTimeZone('UTC'));
            } catch (InvalidFormatException $exception) {
                $dateTime = null;
            }
        } while ($dateTime === null);


        $newOrder = [
            'order_number'         => (string) (10000000 + $this->faker->unique()->randomNumber(7)),
            'total'                => $this->faker->randomNumber(2),
            'shipping_address_id'  => $shippingAddress->getKey(),
            'shipping_method_code' => $this->faker->randomElement(['next_day', 'store_pickup', 'express']),
            'shipping_method_name' => $this->faker->randomElement(['method_name_1', 'method_name_2', 'method_name_3']),
            'order_placed_at'      => $dateTime,
            'status_code'          => $orderStatus->code,
        ];

        if (! $orderStatus->order_active) {
            $user = User::query()->inRandomOrder()->first('id') ?? User::factory()->create();

            $newOrder['order_closed_at'] = $this->faker->dateTimeBetween($newOrder['order_placed_at'], now());
            $newOrder['packer_user_id'] = $user->getKey();
            $newOrder['packed_at'] = $newOrder['order_closed_at'];
        }

        return $newOrder;
    }
}
