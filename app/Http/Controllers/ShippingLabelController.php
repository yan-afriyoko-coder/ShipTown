<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingLabelShowRequest;
use App\Models\ShippingLabel;

class ShippingLabelController extends Controller
{
    public function show(ShippingLabelShowRequest $request, ShippingLabel $shipping_label)
    {
        return base64_decode($shipping_label->base64_pdf_labels);
    }
}
