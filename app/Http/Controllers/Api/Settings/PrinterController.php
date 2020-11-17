<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\PrintService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PrinterController extends Controller
{
    private $printService;

    public function __construct(PrintService $printService)
    {
        $this->printService = $printService;
    }

    public function index(Request $request)
    {
        try {
            $printers = $this->printService->getPrinters();

            return new ResourceCollection(collect($printers));
        } catch (\Exception $e) {
            return response()->json([], 422);
        }
    }

    public function use(Request $request, $printerId)
    {
        $user = $request->user();
        $user->printer_id = $printerId;

        $user->save();

        return new UserResource($user);
    }
}
