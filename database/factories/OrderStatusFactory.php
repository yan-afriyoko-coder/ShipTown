<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrderStatus;

class OrderStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $availableStatuses = [
            'open',
            'closed',
            'pending',
            'ready',
            'on_hold',
            'authorized',
            'paid',
            'fulfilled',
            'overdue',
            'refunded',
            'unpaid',
            'voided',
            'in_transit',
            'canceled',
            'completed',
            'awaiting',
            'cannot_fulfill',
            'packing_dublin',
            'packing_galway',
            'packing_warehouse',
            'ready_for_collection',
            'collection_dublin',
            'collection_galway',
        ];

        $status = $this->faker->randomElement($availableStatuses);

        while (OrderStatus::query()->where(['code' => $status])->exists()) {
            $status = $this->faker->randomElement($availableStatuses);
        }

        return [
            'code'           => $status,
            'name'           => $status,
            'order_active'   => $this->faker->boolean,
            'order_on_hold'  => $this->faker->boolean,
        ];
    }
}
