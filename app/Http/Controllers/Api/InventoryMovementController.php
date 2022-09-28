<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidSelectException;
use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryMovementStoreRequest;
use App\Http\Resources\InventoryMovementResource;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Modules\Reports\src\Models\InventoryMovementsReport;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InventoryMovementController extends Controller
{
    /**
     * @throws InvalidSelectException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $report = new InventoryMovementsReport();

        $resource = $report->queryBuilder()
            ->simplePaginate($request->get('per_page', 10));

//        dd($resource);
//        $inventoryMovement = QueryBuilder::for(InventoryMovement::class)
//            ->allowedFilters([
//                AllowedFilter::exact('description'),
//                AllowedFilter::exact('warehouse_id'),
//            ])
//            ->allowedIncludes([
//                'inventory',
//                'product',
//                'warehouse',
//                'user',
//            ])
//            ->allowedSorts([
//                'id',
//                'created_at'
//            ])
//            ->simplePaginate($request->get('per_page', 10));

        return InventoryMovementResource::collection($resource);
    }

    public function store(InventoryMovementStoreRequest $request): AnonymousResourceCollection
    {
        /** @var Inventory $inventory */
        $inventory = Inventory::query()
            ->where([
                'product_id' => $request->get('product_id'),
                'warehouse_id' => $request->get('warehouse_id'),
            ])
            ->first();

        $inventoryMovement = InventoryService::adjustQuantity(
            $inventory,
            data_get($request->validated(), 'quantity'),
            data_get($request->validated(), 'description'),
        );

        return InventoryMovementResource::collection([$inventoryMovement]);
    }
}
