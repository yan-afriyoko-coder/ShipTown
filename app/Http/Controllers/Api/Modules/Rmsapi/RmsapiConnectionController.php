<?php

namespace App\Http\Controllers\Api\Modules\Rmsapi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConfigurationRmsApiRequest;
use App\Http\Resources\RmsapiConnectionResource;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Exception;
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
        $rmsapiConnection = RmsapiConnection::query()
            ->updateOrCreate(['id' => $request->get('id')], $request->validated());

        return new RmsapiConnectionResource($rmsapiConnection);
    }

    /**
     * @param RmsapiConnection $connection
     *
     * @throws Exception
     *
     * @return Application|ResponseFactory|Response
     */
    public function destroy(RmsapiConnection $connection)
    {
        $connection->delete();

        return response('ok');
    }
}
