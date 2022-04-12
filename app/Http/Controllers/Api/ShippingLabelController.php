<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShippingLabelRequest;
use App\Http\Resources\ShippingLabelResource;
use App\Models\ShippingLabel;

class ShippingLabelController extends Controller
{
    public function store(StoreShippingLabelRequest $request)
    {
//        /** @var Order $order */
//        $order = Order::whereId($request->get('order_id'))->get(['order_number']);
//
//        $pdfString = OrderService::getOrderPdf($order->order_number, 'address_label');
//
//        $printJob = new PrintJob();
//        $printJob->printer_id = $request->user()->printer_id;
//        $printJob->title = 'address_label_'.$order->order_number.'_by_'.$request->user()->id;
//        $printJob->pdf = base64_encode($pdfString);
//
//        PrintNode::print($printJob);
//
//        return PrintJobResource::make($printJob);]
//        $query = ShippingLabel::getSpatieQueryBuilder();
//
        return ShippingLabelResource::collection(collect([]));
    }
}
