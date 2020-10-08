<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Packlist\StoreRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PacklistResource;
use App\Models\Order;
use App\Models\Packlist;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PacklistOrderController extends Controller
{
    public function index(Request $request)
    {
        $order = OrderService::getSpatieQueryBuilder()
            ->whereNull('packed_at')
            ->whereNull('packer_user_id')
            ->first();

        if (!$order) {
            return $this->respondNotFound();
        }

        $wasReserved = Order::where(['id' => $order->id])
            ->where(['updated_at' => $order->updated_at])
            ->whereNull('packer_user_id')
            ->update(['packer_user_id' => $request->user()->getKey()]);

        if (!$wasReserved) {
            return $this->respondBadRequest('Order could not be reserved, try again');
        }

        Order::whereKeyNot($order->id)
            ->where(['packer_user_id' => $request->user()->getKey()])
            ->whereNull('packed_at')
            ->update(['packer_user_id' => null]);

        return new OrderResource($order);
    }

    public function store(StoreRequest $request, Packlist $packlist)
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
