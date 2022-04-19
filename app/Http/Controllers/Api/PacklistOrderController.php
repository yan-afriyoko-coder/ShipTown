<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Packlist\StoreRequest;
use App\Http\Requests\PacklistOrderIndexRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PacklistResource;
use App\Models\Order;
use App\Models\Packlist;
use App\Services\OrderService;
use Illuminate\Http\Request;

/**
 * Class PacklistOrderController.
 */
class PacklistOrderController extends Controller
{
    /**
     * @param PacklistOrderIndexRequest $request
     *
     * @return OrderResource
     */
    public function index(PacklistOrderIndexRequest $request): OrderResource
    {
        /** @var Order $order */
        $order = Order::getSpatieQueryBuilder()
            ->whereNull('packed_at')
            ->whereNull('packer_user_id')
            ->firstOrFail();

        $wasReserved = Order::where(['id' => $order->id])
            ->where(['updated_at' => $order->updated_at])
            ->whereNull('packer_user_id')
            ->update(['packer_user_id' => $request->user()->getKey()]);

        if (!$wasReserved) {
            $this->respondBadRequest('Order could not be reserved, try again');
        }

        Order::whereKeyNot($order->id)
            ->where(['packer_user_id' => $request->user()->getKey()])
            ->whereNull('packed_at')
            ->update(['packer_user_id' => null]);

        $order->log('received order for packing');

        return new OrderResource($order);
    }

    /**
     * @param StoreRequest $request
     * @param Packlist     $packlist
     *
     * @return PacklistResource
     */
    public function store(StoreRequest $request, Packlist $packlist): PacklistResource
    {
        $attributes = array_merge(
            $request->validated(),
            ['packer_user_id' => $request->user()->id]
        );

        $packlist->update($attributes);

        if ($packlist->order->isPacked) {
            // Print address label
            $pdf = OrderService::getOrderPdf($packlist->order->order_number, 'address_label');
            $request->user()->newPdfPrintJob('test', $pdf);
        }

        return new PacklistResource($packlist);
    }
}
