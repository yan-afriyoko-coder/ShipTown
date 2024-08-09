<?php

namespace App\Http\Controllers\Api\DataCollectorActions;

use App\Http\Requests\Api\DataCollectorActions\AddProductStoreRequest;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Inventory;
use App\Models\ProductAlias;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class AddProductController
{
    public function store(AddProductStoreRequest $request): AnonymousResourceCollection
    {
        $dataCollectionRecord = $this->findOrCreateRecord($request);

        $fieldName = $request->has('quantity_scanned') ? 'quantity_scanned' : 'quantity_requested';
        $dataCollectionRecord->increment($fieldName, $request->validated($fieldName, 0));

        return JsonResource::collection(Arr::wrap($dataCollectionRecord));
    }

    public function findOrCreateRecord(AddProductStoreRequest $request): DataCollectionRecord
    {
        $productId = ProductAlias::query()
            ->where(['alias' => $request->validated('sku_or_alias')])
            ->first('product_id')->product_id;

        $dataCollection = DataCollection::query()
            ->find($request->validated('data_collection_id'));

        return $dataCollection->firstOrCreateProductRecord($productId);
    }
}
