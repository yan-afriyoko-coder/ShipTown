<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductInventoryController.
 */
class ProductInventoryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($request->get('per_page') == 'all') {
            return Product::whereHas('inventory', function ($query) {
                $query->where('quantity_reserved', '>', 0);
            })
                ->get()
                ->load('inventory');
        } else {
            return Product::whereHas('inventory', function ($query) {
                $query->where('quantity_reserved', '>', 0);
            })
                ->when($request->has('q'), function ($query) use ($request) {
                    return $query
                        ->where('sku', 'like', '%'.$request->get('q').'%')
                        ->orWhere('name', 'like', '%'.$request->get('q').'%');
                })
                ->when($request->has('sort'), function ($query) use ($request) {
                    return $query
                            ->orderBy($request->get('sort'), $request->get('order', 'asc'));
                })
                ->with('inventory')
                ->paginate(100);
        }
    }

    /**
     * @param StoreInventoryRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function store(StoreInventoryRequest $request): AnonymousResourceCollection
    {
        $product = Product::where('sku', '=', $request->sku)->firstOrFail();

        $update = $request->all();

        $update['product_id'] = $product->id;

        $inventory = Inventory::updateOrCreate(
            [
                'product_id'     => $update['product_id'],
                'location_id'    => $update['location_id'],
                'warehouse_code' => $update['location_id'],
            ],
            $update
        );

        return JsonResource::collection(collect([$inventory]));
    }
}
