<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintDpdLabelStoreRequest;
use App\Http\Resources\PreAdviceResource;
use App\Models\Order;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Exceptions\ConsignmentValidationException;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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

            $preAdvice = $this->createPreAdviceOrFail($order);

            return PreAdviceResource::collection(collect([0 => $preAdvice->toArray()]));

        } catch (ConsignmentValidationException $exception) {
            $this->respondBadRequest($exception->getMessage());
        }

        return $this->respondOK200();
    }

    /**
     * @param Order $order
     * @return PreAdvice
     * @throws ConsignmentValidationException
     */
    private function createPreAdviceOrFail(Order $order): PreAdvice
    {
        $preAdvice = Dpd::shipOrder($order);

        if ($preAdvice->isNotSuccess()) {
            $this->respondBadRequest($preAdvice->consignment()['RecordErrorDetails']);
        }

        return $preAdvice;
    }
}
