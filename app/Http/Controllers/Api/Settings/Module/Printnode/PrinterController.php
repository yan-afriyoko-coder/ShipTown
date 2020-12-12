<?php

namespace App\Http\Controllers\Api\Settings\Module\Printnode;

use App\Http\Controllers\Controller;
use App\Services\PrintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class PrinterController
 * @package App\Http\Controllers\Api\Settings\Module\Printnode
 */
class PrinterController extends Controller
{
    /**
     * @var PrintService
     */
    private $printService;

    /**
     * PrinterController constructor.
     * @param PrintService $printService
     */
    public function __construct(PrintService $printService)
    {
        $this->printService = $printService;
    }

    /**
     * @param Request $request
     * @return JsonResponse|ResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $printers = $this->printService->getPrinters();

            return new ResourceCollection(collect($printers));
        } catch (\Exception $e) {
            return response()->json([], 422);
        }
    }
}
