<?php

namespace App\Http\Controllers\Api\Modules\Magento2msi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Magento2MsiConnectionDestroyRequest;
use App\Http\Requests\Magento2MsiConnectionIndexRequest;
use App\Http\Requests\Magento2MsiConnectionStoreRequest;
use App\Http\Requests\Magento2MsiConnectionUpdateRequest;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Jobs\AssignInventorySourceJob;
use App\Modules\Magento2MSI\src\Jobs\CheckIfSyncIsRequiredJob;
use App\Modules\Magento2MSI\src\Jobs\EnsureProductRecordsExistJob;
use App\Modules\Magento2MSI\src\Jobs\FetchStockItemsJob;
use App\Modules\Magento2MSI\src\Jobs\SyncProductInventoryJob;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class Magento2MsiConnectionController extends Controller
{
    public function index(Magento2MsiConnectionIndexRequest $request): AnonymousResourceCollection
    {
        $connections = Magento2msiConnection::getSpatieQueryBuilder()
            ->allowedSorts([
                'tag.name.en'
            ])
            ->get()
            ->collect();

        $connections = $connections->map(function ($connection) {
            $sourceCodes = MagentoApi::getInventorySources($connection);

            return array_merge($connection->toArray(), [
                'inventory_sources' => data_get($sourceCodes, 'items', [])
            ]);
        });

        return JsonResource::collection($connections);
    }

    public function store(Magento2MsiConnectionStoreRequest $request): JsonResource
    {
        $connection = Magento2msiConnection::create($request->all());

        EnsureProductRecordsExistJob::dispatchAfterResponse();
        CheckIfSyncIsRequiredJob::dispatchAfterResponse();
        FetchStockItemsJob::dispatchAfterResponse();
        SyncProductInventoryJob::dispatchAfterResponse();

        return JsonResource::make($connection);
    }

    public function update(Magento2MsiConnectionUpdateRequest $request, $connection_id): JsonResource
    {
        $connection = Magento2msiConnection::findOrFail($connection_id);

        $connection->update($request->all());

        Magento2msiProduct::query()->where('connection_id', $connection_id)->delete();

        Magento2msiProduct::query()->forceDelete();

        AssignInventorySourceJob::dispatchAfterResponse();
        EnsureProductRecordsExistJob::dispatchAfterResponse();
        CheckIfSyncIsRequiredJob::dispatchAfterResponse();
        FetchStockItemsJob::dispatchAfterResponse();
        SyncProductInventoryJob::dispatchAfterResponse();

        return JsonResource::make($connection);
    }

    public function destroy(Magento2MsiConnectionDestroyRequest $request, $connection_id): JsonResource
    {
        $connection = Magento2msiConnection::findOrFail($connection_id);

        $connection->delete();

        return JsonResource::make($connection);
    }
}
