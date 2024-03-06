<?php

namespace App\Modules\PrintNode\src\Listeners;

use App\Events\ShippingLabel\ShippingLabelCreatedEvent;
use App\Models\ShippingLabel;
use App\Modules\PrintNode\src\PrintNode;
use Exception;
use Illuminate\Http\Client\Response;

class PrintShippingLabelListener
{
    /**
     * @throws Exception
     */
    public function handle(ShippingLabelCreatedEvent $event): Response
    {
        $label = $event->shippingLabel;

        $printer_id = data_get($label, 'user.printer_id');

        return match ($label->content_type) {
            ShippingLabel::CONTENT_TYPE_URL => PrintNode::printPdfFromUrl(base64_decode($label->base64_pdf_labels), $printer_id),
            ShippingLabel::CONTENT_TYPE_PDF => PrintNode::printBase64Pdf($label->base64_pdf_labels, $printer_id),
            ShippingLabel::CONTENT_TYPE_RAW => PrintNode::printRaw($label->base64_pdf_labels, $printer_id),
            default => throw new Exception('Unexpected match value'),
        };
    }
}
