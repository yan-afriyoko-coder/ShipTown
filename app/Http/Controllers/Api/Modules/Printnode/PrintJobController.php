<?php

namespace App\Http\Controllers\Api\Modules\Printnode;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrintJobRequest;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\PrintNode\src\Resources\PrintJobResource;

/**
 * Class PrintJobController.
 */
class PrintJobController extends Controller
{
    /**
     * @param StorePrintJobRequest $request
     *
     * @return PrintJobResource
     */
    public function store(StorePrintJobRequest $request): PrintJobResource
    {
        $printJob = new PrintJob($request->validated());

        PrintNode::print($printJob);

        return new PrintJobResource($printJob);
    }
}
