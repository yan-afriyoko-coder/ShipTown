<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Picklist\StoreRequest;
use App\Http\Resources\PicklistResource;
use App\Models\Picklist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PicklistController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Picklist::class)
            ->allowedIncludes([
                'product',
                'product.aliases',
                'inventory',
                'order',
            ])
            ->allowedFilters([
                'inventory_source_quantity',
                AllowedFilter::scope('inventory_source_location_id', 'InventorySource'),
                AllowedFilter::scope('single_line_orders_only', 'WhereIsSingleLineOrder'),
                AllowedFilter::scope('in_stock_only'),
                AllowedFilter::scope('start_from_shelf', 'MinimumShelfLocation'),
            ])
            ->allowedSorts([
                'inventory_source_shelf_location',
                'picklists.sku_ordered'
            ])
            ->whereNull('picked_at');

        $per_page = $request->get('per_page', 30);

        return $query->paginate($per_page);
    }

    public function store(StoreRequest $request, Picklist $picklist)
    {
        $attributes = array_merge(
            $request->validated(),
            ['picker_user_id' => $request->user()->id]
        );

        $picklist->update($attributes);

        return new PicklistResource($picklist);
    }
}
