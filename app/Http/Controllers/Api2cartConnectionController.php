<?php

namespace App\Http\Controllers;

use App\Models\Api2cartConnection;
use App\Http\Requests\StoreApi2cartConnectionRequest;
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

    public function destroy(Api2cartConnection $rms_api_configuration)
    {
        $rms_api_configuration->delete();

        return response('ok');
    }
}
