<?php

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;

class DpdIrelandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(env('TEST_DPD_USER')) {
            \App\Modules\DpdIreland\src\Models\DpdIreland::factory()->create();
            $this->createTestOrder();
        }
    }

    /**
     * @return void
     */
    private function createTestOrder(): void
    {
        /** @var OrderAddress $testAddress */
        $testAddress = OrderAddress::factory()->make();
        $testAddress->first_name = 'John';
        $testAddress->last_name = 'Smith';
        $testAddress->phone = '12345678901';
        $testAddress->company = "DPD Group Ltd";
        $testAddress->country_code = "IE";
        $testAddress->postcode = "B661BY";
        $testAddress->address1 = "DPD Ireland";
        $testAddress->address2 = "Unit 2B Midland Gateway Bus";
        $testAddress->city = "Westmeath";
        $testAddress->state_code = "Kilbeggan";
        $testAddress->email = 'john.smith@dpd.ie';
        $testAddress->save();

        /** @var Order $order */
        $order = Order::factory()->make();
        $order->order_number = 'TEST-IRL';
        $order->shippingAddress()->associate($testAddress);
        $order->save();
        OrderProduct::factory()->count(3)->create(['order_id' => $order->getKey()]);
    }
}
