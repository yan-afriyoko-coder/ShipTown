<?php

namespace App\Http\Controllers\Api\Settings\Module\Rmsapi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConfigurationRmsApiRequest;
use App\Http\Resources\RmsapiConnectionResource;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * Class RmsapiConnectionController.
 */
class RmsapiConnectionController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return RmsapiConnectionResource::collection(RmsapiConnection::all());
    }

    /**
     * @param StoreConfigurationRmsApiRequest $request
     *
     * @return RmsapiConnectionResource
     */
    public function store(StoreConfigurationRmsApiRequest $request)
    {
        $config = new RmsapiConnection();
        $config->fill($request->only($config->getFillable()));
        $config->save();

        return new RmsapiConnectionResource($config);
    }

    /**
     * @param RmsapiConnection $connection
     *
     * @throws \Exception
     *
     * @return Application|ResponseFactory|Response
     */
    public function destroy(RmsapiConnection $connection)
    {
        $connection->delete();

        return response('ok');
    }
}
