<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Packlist\StoreRequest;
use App\Http\Resources\PacklistResource;
use App\Models\Packlist;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PacklistController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Packlist::class)
            ->allowedFilters([
                AllowedFilter::exact('order_id'),
                AllowedFilter::exact('is_picked'),
                AllowedFilter::exact('is_packed'),
                AllowedFilter::exact('order.order_number'),
                AllowedFilter::scope('inventory_source_location_id', 'addInventorySource')->default(100),
            ])
            ->allowedIncludes([
                'product',
                'product.aliases',
                'user',
                'order'
            ]);

        return $query->paginate(999);
    }

    public function store(StoreRequest $request, Packlist $packlist)
    {
        $attributes = array_merge(
            $request->validated(),
            ['packer_user_id' => $request->user()->id]
        );

        $packlist->update($attributes);

        return new PacklistResource($packlist);
    }
}
