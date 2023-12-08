<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingLabelShowRequest;
use App\Models\ShippingLabel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class ShippingLabelController extends Controller
{
    public function show(ShippingLabelShowRequest $request, ShippingLabel $shipping_label): Response|Application|ResponseFactory
    {
        return response(base64_decode($shipping_label->base64_pdf_labels))
            ->header('Content-Type', 'application/pdf');
    }
}
