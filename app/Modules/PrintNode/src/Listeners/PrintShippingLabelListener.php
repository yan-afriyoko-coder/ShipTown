<?php

namespace App\Modules\PrintNode\src\Listeners;

use App\Events\ShippingLabelCreatedEvent;
use App\Modules\PrintNode\src\PrintNode;

class PrintShippingLabelListener
{
    public function handle(ShippingLabelCreatedEvent $event)
    {
        $printer_id = data_get($event->shippingLabel, 'user.printer_id');

        PrintNode::printBase64Pdf($event->shippingLabel->base64_pdf_labels, $printer_id);
    }
}
