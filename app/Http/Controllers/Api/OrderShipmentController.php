<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderShipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class OrderShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return $this->getResponse(405, 'Method not allowed');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        return $this->getResponse(405, 'Method not allowed');
    }

    /**
     * Display the specified resource.
     *
     * @param OrderShipment $orderShipment
     * @return JsonResponse
     */
    public function show(OrderShipment $orderShipment)
    {
        return $this->getResponse(405, 'Method not allowed');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param OrderShipment $orderShipment
     * @return JsonResponse
     */
    public function update(Request $request, OrderShipment $orderShipment)
    {
        return $this->getResponse(405, 'Method not allowed');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OrderShipment $orderShipment
     * @return JsonResponse
     */
    public function destroy(OrderShipment $orderShipment)
    {
        return $this->getResponse(405, 'Method not allowed');
    }
}
