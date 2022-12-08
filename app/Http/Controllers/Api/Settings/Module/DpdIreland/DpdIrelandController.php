<?php

namespace App\Http\Controllers\Api\Settings\Module\DpdIreland;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDpdIrelandRequest;
use App\Http\Resources\DpdIrelandConfigurationResource;
use App\Modules\DpdIreland\src\Client;
use App\Modules\DpdIreland\src\Models\DpdIreland;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DpdIrelandController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $connection = DpdIreland::firstOrFail();

        return DpdIrelandConfigurationResource::make($connection);
    }

    public function store(StoreDpdIrelandRequest $request): JsonResource
    {
        $connection = DpdIreland::updateOrCreate([], $request->validated());

        Client::clearCache();

        return DpdIrelandConfigurationResource::make($connection);
    }

    public function destroy(Request $request, int $connection_id): DpdIrelandConfigurationResource
    {
        $connection = DpdIreland::findOrFail($connection_id);

        $connection->delete();

        return DpdIrelandConfigurationResource::make($connection);
    }
}
