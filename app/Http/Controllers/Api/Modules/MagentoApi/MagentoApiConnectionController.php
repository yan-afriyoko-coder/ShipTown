<?php

namespace App\Http\Controllers\Api\Modules\MagentoApi;

use App\Http\Controllers\Controller;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\MagentoApi\src\Http\Requests\MagentoApiConnectionDestroyRequest;
use App\Modules\MagentoApi\src\Http\Requests\MagentoApiConnectionIndexRequest;
use App\Modules\MagentoApi\src\Http\Requests\MagentoApiConnectionStoreRequest;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Tags\Tag;

class MagentoApiConnectionController extends Controller
{
    public function index(MagentoApiConnectionIndexRequest $request): AnonymousResourceCollection
    {
        return JsonResource::collection(MagentoConnection::all());
    }

    public function store(MagentoApiConnectionStoreRequest $request): JsonResource
    {
        $config = new MagentoConnection();
        $config->fill($request->only($config->getFillable()));

        $tag = Tag::findOrCreate($request->tag);

        $config->inventory_source_warehouse_tag_id = $tag->id;
        $config->save();

        return new JsonResource($config);
    }

    public function destroy(MagentoApiConnectionDestroyRequest $request, $id)
    {
        $connection = MagentoConnection::findOrFail($id);
        $connection->delete();
        return response('ok');
    }
}
