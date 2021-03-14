<?php

namespace App\Http\Controllers\Api\Settings\Module\Printnode;

use App\Http\Controllers\Controller;
use App\Modules\PrintNode\src\PrintNode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PrinterController
 * @package App\Http\Controllers\Api\Settings\Module\Printnode
 */
class PrinterController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResource
     */
    public function index(Request $request): JsonResource
    {
        return new JsonResource(PrintNode::getPrinters());
    }
}
