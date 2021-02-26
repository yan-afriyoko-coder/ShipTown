<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintDpdLabelStoreRequest;
use App\Http\Resources\PreAdviceResource;
use App\Models\Order;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Exceptions\AuthorizationException;
use App\Modules\DpdIreland\src\Exceptions\ConsignmentValidationException;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\Services\PrintService;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use PrintNode\Response;

/**
 * Class PrintDpdLabelController
 * @package App\Http\Controllers\Api
 */
class PrintDpdLabelController extends Controller
{
    /**ø
     * @param PrintDpdLabelStoreRequest $request
     * @param string $order_number
     * @return AnonymousResourceCollection
     */
    public function store(PrintDpdLabelStoreRequest $request, string $order_number): AnonymousResourceCollection
    {
        $order = Order::whereOrderNumber($order_number)->firstOrFail();

        $preAdvice = $this->createPreAdviceOrFail($order);

        $printRequest = $this->printOrFail($order_number, $preAdvice);

        return PreAdviceResource::collection(
            collect()->add($preAdvice->toArray())
        );
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

            return $preAdvice;
        } catch (AuthorizationException $exception) {
            $this->respond403Forbidden($exception->getMessage());
        } catch (Exception $exception) {
            $this->respondBadRequest($exception->getMessage());
        }
    }

    /**
     * @param string $job_title
     * @param PreAdvice $preAdvice
     * @return Response|null
     */
    public function printOrFail(string $job_title, PreAdvice $preAdvice): ?Response
    {
        try {
            $full_job_title = $job_title . '_by_' . request()->user()->id;

            return PrintService::print()->printPdfFromUrl(
                request()->user()->printer_id,
                $full_job_title,
                $preAdvice->labelImage()
            );
        } catch (ConsignmentValidationException $exception) {
            $this->respondBadRequest($exception->getMessage());
        }
    }
}
