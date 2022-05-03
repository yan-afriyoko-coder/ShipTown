<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\ShippingServiceAbstract;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShippingLabelRequest;
use App\Models\ShippingService;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

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
        $shippingService = ShippingService::query()
            ->where(['code' => $request->validated()['shipping_service_code']])
            ->firstOrFail();

        try {
            /** @var ShippingServiceAbstract $shipper */
            $shipper = app($shippingService->service_provider_class);

            $shippingLabelResourceCollection = $shipper->ship($request->validated()['order_id']);
        } catch (Exception $exception) {
            report($exception);
            $this->respond503ServiceUnavailable($exception->getMessage());
        }

        return $shippingLabelResourceCollection ?? JsonResource::collection([]);
    }
}
