<?php

namespace App\Http\Controllers\Api\Settings\Module\DpdIreland;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDpdIrelandRequest;
use App\Http\Resources\DpdIrelandConfigurationResource;
use App\Modules\DpdIreland\src\Client;
use App\Modules\DpdIreland\src\Models\DpdIreland;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DpdIrelandController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $config = DpdIreland::firstOrFail();

        return DpdIrelandConfigurationResource::make($config);
    }

    public function store(StoreDpdIrelandRequest $request): JsonResource
    {
        $config = DpdIreland::updateOrCreate([], $request->validated());

        Client::clearCache();

        return DpdIrelandConfigurationResource::make($config);
    }

    /**
     * @throws Exception
     */
    public function destroy(Request $request, DpdIreland $connection)
    {
        $connection->delete();

        return DpdIrelandConfigurationResource::make($connection);
    }
}
