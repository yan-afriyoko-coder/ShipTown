<?php

namespace App\Http\Controllers\Api\Settings\Module\Api2cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApi2cartConnectionRequest;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Http\Resources\Json\JsonResource;

class Api2cartConnectionController extends Controller
{
    public function index()
    {
        return JsonResource::collection(Api2cartConnection::all());
    }

    public function store(StoreApi2cartConnectionRequest $request)
    {
        $config = new Api2cartConnection();
        $config->fill($request->only($config->getFillable()));
        $config->save();

        return new JsonResource($config);
    }

    public function destroy(Api2cartConnection $api2cart_configuration)
    {
        $api2cart_configuration->delete();
        return response('ok');
    }
}
