<?php

namespace App\Http\Controllers\Api\Settings\Module\Api2cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApi2cartConnectionRequest;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

/**
 * Class Api2cartConnectionController
 * @package App\Http\Controllers\Api\Settings\Module\Api2cart
 */
class Api2cartConnectionController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(Api2cartConnection::all());
    }

    /**
     * @param StoreApi2cartConnectionRequest $request
     * @return JsonResource
     */
    public function store(StoreApi2cartConnectionRequest $request): JsonResource
    {
        $config = new Api2cartConnection();
        $config->fill($request->only($config->getFillable()));
        $config->save();

        DispatchImportOrdersJobs::dispatch();

        return new JsonResource($config);
    }

    /**
     * @param Api2cartConnection $api2cart_configuration
     * @return Application|ResponseFactory|Response
     * @throws Exception
     */
    public function destroy(Api2cartConnection $api2cart_configuration)
    {
        $api2cart_configuration->delete();
        return response('ok');
    }
}
