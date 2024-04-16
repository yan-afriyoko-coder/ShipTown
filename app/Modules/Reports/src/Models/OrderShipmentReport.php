<?php

namespace App\Modules\Reports\src\Models;

use App\Models\OrderShipment;

class OrderShipmentReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Order Shipments';

        $this->baseQuery = OrderShipment::query()
            ->leftJoin('orders as order', 'orders_shipments.order_id', '=', 'order.id')
            ->leftJoin('users as user', 'orders_shipments.user_id', '=', 'user.id');

        $this->defaultSelect = implode(',', [
            'user_name',
            'shipping_number',
            'order_number',
            'created_at',
        ]);

        $this->fields = [
            'user_name' => 'user.name',
            'shipping_number' => 'orders_shipments.shipping_number',
            'order_number' => 'order.order_number',
            'created_at' => 'orders_shipments.created_at',
        ];

        $this->casts = [
            'user_name' => 'string',
            'shipping_number' => 'string',
            'order_number' => 'string',
            'created_at' => 'datetime',
        ];
    }
}
