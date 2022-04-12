<?php

namespace App\Modules\DpdIreland\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Http\Resources\PreAdviceResource;
use App\Models\Order;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Exceptions\AuthorizationException;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\PrintNode;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DpdShippingService extends ShippingServiceAbstract
{
    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function ship(int $order_id): AnonymousResourceCollection
    {
        /** @var Order $order */
        $order = Order::findOrFail($order_id);

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
     * @throws Exception|GuzzleException
     */
    private function createPreAdviceOrFail(Order $order): PreAdvice
    {
        try {
            $preAdvice = Dpd::shipOrder($order, request()->user());

            if ($preAdvice->isNotSuccess()) {
                throw new Exception('DPD Responded: '.$preAdvice->consignment()['RecordErrorDetails']);
            }

            return $preAdvice;
        } catch (AuthorizationException | Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param PreAdvice $preAdvice
     *
     * @return int
     * @throws Exception
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
            throw new Exception($exception->getMessage());
        }
    }
}
