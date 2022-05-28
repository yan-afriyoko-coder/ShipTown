<?php

return [
    'class' => \App\Events\Order\ActiveOrderCheckEvent::class,
    'description' => 'On Active Order Event',
    'conditions' => [
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition::class,
            'description' => 'Status Code is',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\StatusCodeNotInOrderCondition::class,
            'description' => 'Status Code NOT In',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationOrderCondition::class,
            'description' => 'Can Fulfill from Warehouse Code (0 for all)',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\CanNotFulfillFromLocationOrderCondition::class,
            'description' => 'Can NOT Fulfill Warehouse Code (0 for all)',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsOrderCondition::class,
            'description' => 'Shipping Method Code equals',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\ShippingMethodCodeInOrderCondition::class,
            'description' => 'Shipping Method Code in',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\LineCountEqualsOrderCondition::class,
            'description' => 'Line count equals',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\TotalQuantityToShipEqualsOrderCondition::class,
            'description' => 'Total Quantity To Ship',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\IsPartiallyPaidOrderCondition::class,
            'description' => 'Is Partially Paid',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\IsFullyPaidOrderCondition::class,
            'description' => 'Is Fully Paid',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\IsFullyPickedOrderCondition::class,
            'description' => 'Is Fully Picked',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\IsFullyPackedOrderCondition::class,
            'description' => 'Is Fully Packed',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\OrderNumberEqualsOrderCondition::class,
            'description' => 'Order Number equals',
        ],
    ],
    'actions' => [
        [
            'class' => \App\Modules\Automations\src\Actions\Order\SetStatusCodeAction::class,
            'description' => 'Set Status Code',
        ],
        [
            'class' => \App\Modules\Automations\src\Actions\Order\AddOrderCommentAction::class,
            'description' => 'Add order comment',
        ],
        [
            'class' => \App\Modules\Automations\src\Actions\Order\LogMessageAction::class,
            'description' => 'Add log message',
        ],
        [
            'class' => \App\Modules\Automations\src\Actions\SetLabelTemplateAction::class,
            'description' => 'Set courier label template',
        ],
        [
            'class' => \App\Modules\Automations\src\Actions\Order\SplitOrderToWarehouseCodeAction::class,
            'description' => 'Split Order to warehouse code',
        ],
        [
            'class' => \App\Modules\Automations\src\Actions\Order\ShipRemainingProductsAction::class,
            'description' => 'Mark remaining products as shipped',
        ],
        [
            'class' => \App\Modules\Automations\src\Actions\PushToBoxTopOrderAction::class,
            'description' => 'Create Warehouse Shipment in BoxTop Software',
        ],
        [
            'class' => \App\Modules\Automations\src\Actions\SendOrderEmailAction::class,
            'description' => 'Send email template to customer',
        ],
//        [
//            'class' => \App\Modules\Automations\src\Actions\SplitBundleSkuAction::class,
//            'description' => 'Split bundle SKU (format: BundleSKU,SKU1,SKU2...)',
//        ],
    ]
];
