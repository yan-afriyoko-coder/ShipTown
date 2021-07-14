<?php

namespace App\Http\Controllers\Api\Modules\DpdIreland;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintDpdLabelStoreRequest;
use App\Http\Resources\PreAdviceResource;
use App\Models\Order;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Exceptions\AuthorizationException;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\PrintNode;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class PrintDpdLabelController.
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

        $this->printOrFail($preAdvice);

        return PreAdviceResource::collection(
            collect()->add($preAdvice->toArray())
        );
    }

    /**
     * @param Order $order
     *
     * @return PreAdvice
     */
    private function createPreAdviceOrFail(Order $order): PreAdvice
    {
        $preAdvice = null;

        try {
            $preAdvice = Dpd::shipOrder($order, request()->user());

            if ($preAdvice->isNotSuccess()) {
                $this->respondBadRequest('DPD Responded: '.$preAdvice->consignment()['RecordErrorDetails']);
            }

            return $preAdvice;
        } catch (AuthorizationException $exception) {
            $this->respond403Forbidden($exception->getMessage());
        } catch (Exception $exception) {
            $this->respondBadRequest($exception->getMessage());
        }
    }

    /**
     * @param PreAdvice $preAdvice
     *
     * @return int
     */
    public function printOrFail(PreAdvice $preAdvice): int
    {
        try {
            $printJob = new PrintJob();
            $printJob->printer_id = request()->user()->printer_id;
            $printJob->title = $preAdvice->trackingNumber().'_by_'.request()->user()->id;
            $printJob->pdf_url = $preAdvice->labelImage();

            return PrintNode::print($printJob);
        } catch (Exception $exception) {
            $this->respondBadRequest($exception->getMessage());
        }
    }
}
