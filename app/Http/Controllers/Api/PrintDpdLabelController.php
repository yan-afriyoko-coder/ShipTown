<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintDpdLabelStoreRequest;
use App\Http\Resources\PreAdviceResource;
use App\Models\Order;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Consignment;
use App\Modules\DpdIreland\src\Exceptions\ConsignmentValidationException;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Class PrintDpdLabelController
 * @package App\Http\Controllers\Api
 */
class PrintDpdLabelController extends Controller
{
    /**
     * @param PrintDpdLabelStoreRequest $request
     * @param string $order_number
     * @return AnonymousResourceCollection
     */
    public function store(PrintDpdLabelStoreRequest $request, string $order_number): AnonymousResourceCollection
    {
        try {
            $order = Order::whereOrderNumber($order_number)->firstOrFail();
            $shipping_address = $order->shippingAddress()->first();

            $consignment = new Consignment([
                'RecordID' => $order->order_number,
                'DeliveryAddress' => [
                    'Contact' => $shipping_address->full_name,
                    'ContactTelephone' => $shipping_address->phone,
                    'ContactEmail' => '',
                    'AddressLine1' => $shipping_address->address1,
                    'AddressLine2' => $shipping_address->address2,
                    'AddressLine3' => $shipping_address->city,
                    'AddressLine4' => $shipping_address->state_name ?: $shipping_address->city,
                    'CountryCode' => $shipping_address->country_code,
                ]
            ]);

            $preAdvice = Dpd::getPreAdvice($consignment);

            if ($preAdvice->isNotSuccess()) {
                $this->respondBadRequest($preAdvice->consignment()['RecordErrorDetails']);
            }

            return PreAdviceResource::collection(collect([0 => $preAdvice->toArray()]));

        } catch (ConsignmentValidationException $exception) {
            $this->respondBadRequest($exception->getMessage());
        }

    }
}
