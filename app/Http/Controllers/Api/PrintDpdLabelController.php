<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintDpdLabelStoreRequest;
use App\Http\Resources\PreAdviceResource;
use App\Models\Order;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Exceptions\AuthorizationException;
use App\Modules\DpdIreland\src\Exceptions\ConsignmentValidationException;
use App\Modules\DpdIreland\src\Exceptions\PreAdviceRequestException;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\Services\PrintService;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Sentry\Client;
use Sentry\Event;
use function request;

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
     * @throws PreAdviceRequestException
     */
    public function store(PrintDpdLabelStoreRequest $request, string $order_number): AnonymousResourceCollection
    {
        try {
            $order = Order::whereOrderNumber($order_number)->firstOrFail();

            $preAdvice = $this->createPreAdviceOrFail($order);

            $job_name = $order_number . '_by_' . $request->user()->id;

            info('Label generated', ['url' => $preAdvice->labelImage()]);

            PrintService::print()->printPdfFromUrl($request->user()->printer_id, $job_name, $preAdvice->labelImage());

            return PreAdviceResource::collection(collect([0 => $preAdvice->toArray()]));

        } catch (ConsignmentValidationException $exception) {
            $this->respondBadRequest($exception->getMessage());
        }

        return $this->respondOK200();
    }

    /**
     * @param Order $order
     * @return PreAdvice
     */
    private function createPreAdviceOrFail(Order $order): PreAdvice
    {
        $preAdvice = null;

        try {
            $preAdvice = Dpd::shipOrder($order, request()->user());

            if ($preAdvice->isNotSuccess()) {
                $this->respondBadRequest($preAdvice->consignment()['RecordErrorDetails']);
            }

        } catch (AuthorizationException $exception){
            $this->respond403Forbidden($exception->getMessage());

        } catch (Exception $exception) {
            $this->respondBadRequest($exception->getMessage());
        }

        return $preAdvice;
    }
}
