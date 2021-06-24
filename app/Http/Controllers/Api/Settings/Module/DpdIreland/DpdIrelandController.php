<?php

namespace App\Http\Controllers\Api\Settings\Module\DpdIreland;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDpdIrelandRequest;
use App\Modules\DpdIreland\src\Client;
use App\Modules\DpdIreland\src\Models\DpdIreland;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DpdIrelandController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $config = DpdIreland::first();

        if (!$config) {
            $this->respondNotFound('No Dpd Ireland Configuration Found');
        } else {
            return JsonResource::make($config);
        }
    }

    public function store(StoreDpdIrelandRequest $request): JsonResource
    {
        $config = DpdIreland::first();

        if (!$config) {
            $config = DpdIreland::query()->create($request->validated());
        } else {
            $config->update($request->validated());
        }

        Client::clearCache();

        return JsonResource::make($config);
    }
}
