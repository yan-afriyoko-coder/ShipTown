<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\ShippingServiceAbstract;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShippingLabelRequest;
use App\Http\Resources\ShippingLabelResource;
use App\Models\OrderShipment;
use App\Models\ShippingService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 *
 */
class ShippingLabelController extends Controller
{
    /**
     * @param StoreShippingLabelRequest $request
     * @return AnonymousResourceCollection
     */
    public function store(StoreShippingLabelRequest $request): AnonymousResourceCollection
    {
        /** @var ShippingService $shippingService */
        $shippingService = ShippingService::whereCode($request->validated()['shipping_service_code'])
            ->firstOrFail();

        try {
            /** @var ShippingServiceAbstract $shipper */
            $shipper = app($shippingService->service_provider_class);

            $shipper->ship($request->validated()['order_id']);
        } catch (\Exception $exception) {
            report($exception);
            $this->respond503ServiceUnavailable($exception->getMessage());
        }

        $query = OrderShipment::getSpatieQueryBuilder();

        return ShippingLabelResource::collection($this->getPaginatedResult($query));
    }
}
