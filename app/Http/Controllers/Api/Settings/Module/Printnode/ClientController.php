<?php

namespace App\Http\Controllers\Api\Settings\Module\Printnode;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrintNodeClientRequest;
use App\Modules\PrintNode\src\Models\Client;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\PrintNode\src\Resources\PrintNodeClientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PrintNodeClientResource::collection($this->getPaginatedResult(Client::getSpatieQueryBuilder()));
    }

    public function store(StorePrintNodeClientRequest $request): PrintNodeClientResource
    {
        $printNodeClient = Client::query()->firstOrNew([], []);
        $printNodeClient->fill($request->validated());

        if (!PrintNode::noop($printNodeClient)) {
            abort(400, 'NOOP call failed');
        }

        $printNodeClient->save();

        return PrintNodeClientResource::make($printNodeClient);
    }
}
