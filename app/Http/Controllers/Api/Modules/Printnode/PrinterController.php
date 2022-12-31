<?php

namespace App\Http\Controllers\Api\Modules\Printnode;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintNode\PrinterController\IndexRequest;
use App\Modules\PrintNode\src\PrintNode;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PrinterController.
 */
class PrinterController extends Controller
{
    /**
     * @param IndexRequest $request
     *
     * @return JsonResource
     */
    public function index(IndexRequest $request): JsonResource
    {
        return new JsonResource(PrintNode::getPrinters());
    }
}
