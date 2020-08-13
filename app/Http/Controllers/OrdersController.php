<?php

namespace App\Http\Controllers;

use App\Events\EventTypes;
use App\Http\Requests\DeleteOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Managers\Config;
use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrdersController extends Controller
{

    public function index(Request $request)
    {
        $query = QueryBuilder::for(Order::class)
            ->allowedFilters([
                'order_number',
                AllowedFilter::scope('is_picked'),
                AllowedFilter::scope('is_packed'),
            ]);

        return $query->paginate(10);
    }

    public function store(StoreOrderRequest $request)
    {
        $order = Order::query()->updateOrCreate(
            ['order_number' => $request->order_number],
            $request->all()
        );

        return response()->json($order, 200);
    }

    public function destroy($order_number)
    {
        try {
            $order = Order::query()->where('order_number', $order_number)->firstOrFail();
        }
        catch (ModelNotFoundException $e)
        {
            return $this->respond_NotFound();
        }

       $order->delete();

       return $this->respond_OK_200();
    }
}
