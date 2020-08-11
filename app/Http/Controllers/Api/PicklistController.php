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
use Spatie\QueryBuilder\QueryBuilder;

class PicklistController extends Controller
{
    public function index(Request $request)
    {

        $inventory_location_id = 100;
        $single_line_orders_only = $request->get('single_line_orders_only', 'false') === 'true';
        $currentLocation = $request->get('currentLocation', null);
        $per_page = $request->get('per_page', 3);

        $query = QueryBuilder::for(Picklist::class)
            ->allowedIncludes('product.aliases')
            ->select([
                'picklists.*',
                'pick_location_inventory.shelve_location'
            ])
            ->whereNull('picked_at')
            ->leftJoin('inventory as pick_location_inventory',
                function (JoinClause $join) use ($inventory_location_id) {
                    $join->on('pick_location_inventory.product_id', '=', 'picklists.product_id');
                    $join->on('pick_location_inventory.location_id', '=', DB::raw($inventory_location_id));
                })
            ->with([
                'product',
                'order',
//                'product.aliases',
                'inventory' => function(HasMany $query) use ($inventory_location_id) {
                    $query->where('location_id', '=', $inventory_location_id);
                },
            ])
            ->when(isset($currentLocation),
                function (Builder $query) use ($currentLocation) {
                    return $query->where('pick_location_inventory.shelve_location', '>=', $currentLocation);
                })
            ->when($single_line_orders_only,
                function (Builder $query) {
                    return $query->whereHas('order', function (Builder $query) {
                        return $query->where('product_line_count', '=', 1);
                    });
                })

            ->orderBy('pick_location_inventory.shelve_location')
            ->orderBy('picklists.sku_ordered');

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
