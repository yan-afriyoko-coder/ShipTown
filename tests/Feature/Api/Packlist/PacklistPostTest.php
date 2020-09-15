<?php

namespace Tests\Feature\Api\Packlist;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Services\OrderService;
use App\Services\PrintService;
use App\Services\PacklistService;
use App\User;
use Laravel\Passport\Passport;
use PrintNode\Request;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PacklistPostTest extends TestCase
{
//    /**
//     * @return void
//     */
//    public function testPrintsAddressLabelWhenOrderPacked()
//    {
//        Passport::actingAs(
//            factory(User::class)->create()
//        );
//        $orderProduct = factory(OrderProduct::class)->create([
//            'order_id' => factory(Order::class)->create()->id
//        ]);
//        $order = $orderProduct->order;
//        OrderService::createPickRequests($order);
//        $this->addToPacklist($order);
//
//        foreach ($order->packlist as $packlist) {
//            $packlist->is_packed = true;
//            $packlist->packed_at = now();
//            $packlist->save();
//        }
//
//        // Set the last packlist item as 'unpacked' so we can test
//        $packlist->is_packed = false;
//        $packlist->packed_at = null;
//        $packlist->save();
//
//        // Ensures that the print job is created when the order is packed
//        $this->mock(PrintService::class, function ($mock) {
//            $mock->shouldReceive('newPdfPrintJob')->once();
//        });
//
//        $response = $this->postJson('api/packlist/' . $packlist->getKey(), [
//            'is_packed' => true,
//            'quantity_packed' => $packlist->quantity_requested
//        ]);
//
//        $response->assertStatus(200);
//    }

    /**
     * @return void
     */
    public function testDoNotPrintAddressLabelWhenOrderNotPacked()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );
        $orderProduct = factory(OrderProduct::class)->create([
            'order_id' => factory(Order::class)->create()->id
        ]);
        $order = $orderProduct->order;
        OrderService::createPickRequests($order);
        $this->addToPacklist($order);

        // Ensures that the print job is created when the order is packed
        $this->mock(PrintService::class, function ($mock) {
            $mock->shouldReceive('newPdfPrintJob')->times(0);
        });

        $response = $this->postJson('api/packlist/' . $order->packlist->first()->getKey(), [
            'is_packed' => false,
            'quantity_packed' => 1,
        ]);

        $response->assertStatus(200);
    }

    private function addToPacklist(Order $order)
    {
        foreach ($order->orderProducts()->get() as $orderProduct) {
            $orderProducts = $order->orderProducts()->get();
            PacklistService::addOrderProductPick($orderProduct);
        }
    }
}
