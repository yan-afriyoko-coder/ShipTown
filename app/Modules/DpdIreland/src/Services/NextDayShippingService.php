<?php

namespace App\Modules\DpdIreland\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Exceptions\AuthorizationException;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\PrintNode;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class NextDayShippingService extends ShippingServiceAbstract
{
    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function ship(int $order_id): Collection
    {
        /** @var Order $order */
        $order = Order::findOrFail($order_id);

        $preAdvice = $this->createPreAdviceOrFail($order);

        $shippingLabel = new ShippingLabel();
        $shippingLabel->order_id = $order_id;
        $shippingLabel->user_id = Auth::id();
        $shippingLabel->carrier = 'DPD Ireland';
        $shippingLabel->service = 'next_day';
        $shippingLabel->shipping_number = $preAdvice->trackingNumber();
        $shippingLabel->tracking_url = 'https://dpd.ie/tracking?consignmentNumber=' . $preAdvice->trackingNumber();
        $shippingLabel->content_type = ShippingLabel::CONTENT_TYPE_URL;
        $shippingLabel->base64_pdf_labels = base64_encode($preAdvice->labelImage());
        $shippingLabel->save();

        return collect()->add($shippingLabel);
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
        if (! request()->user()) {
            return -1;
        }

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
