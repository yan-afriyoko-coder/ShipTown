<?php

namespace App\Http\Controllers\Api\Modules\Printnode;

use App\Http\Controllers\Controller;
use App\Modules\PrintNode\src\Http\Requests\PrintJobStoreRequest;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\PrintNode\src\Resources\PrintJobResource;
use Exception;

/**
 * Class PrintJobController.
 */
class PrintJobController extends Controller
{
    /**
     * @param PrintJobStoreRequest $request
     *
     * @return PrintJobResource
     * @throws Exception
     */
    public function store(PrintJobStoreRequest $request): PrintJobResource
    {
        $printJob = new PrintJob($request->validated());

        PrintNode::print($printJob);

        return new PrintJobResource($printJob);
    }
}
