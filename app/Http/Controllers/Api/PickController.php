<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PickRequestUpdateRequest;
use App\Models\Pick;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PickController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $pick = QueryBuilder::for(Pick::class)
            ->allowedFilters([
                AllowedFilter::scope('not_picked_only', 'whereNotPicked'),
                AllowedFilter::scope('inventory_source_id', 'addInventorySource')->default(100),
                AllowedFilter::scope('current_shelf_location', 'MinimumShelfLocation'),
            ])
            ->allowedIncludes([
                'product',
                'product.aliases',
            ])
            ->allowedSorts([
                'inventory_source_shelf_location',
                'picklists.sku_ordered'
            ]);

        $per_page = $request->get('per_page', 3);

        return $pick->paginate($per_page)->appends($request->query());
    }

    /**
     * Display the specified resource.
     *
     * @param Pick $pick
     * @return Response
     */
    public function show(Pick $pick)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PickRequestUpdateRequest $request
     * @param Pick $pick
     * @return JsonResource
     */
    public function update(PickRequestUpdateRequest $request, Pick $pick)
    {
        $picker = $request->user();

        $pick->pick($picker, $request->get('quantity_picked'));

        return new JsonResource($pick);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Pick $pick
     * @return JsonResource
     * @throws Exception
     */
    public function destroy(Pick $pick)
    {
        if ($pick->is_picked) {
            $this->respondBadRequest('Already picked, cannot delete');
        }

        $pick->delete();

        return new JsonResource($pick);
    }
}
