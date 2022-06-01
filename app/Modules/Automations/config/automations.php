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
            'class' => \App\Modules\Automations\src\Conditions\Order\StatusCodeInCondition::class,
            'description' => 'Status Code In',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\StatusCodeNotInCondition::class,
            'description' => 'Status Code NOT In',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationCondition::class,
            'description' => 'Can Fulfill from Warehouse Code (0 for all)',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\CanNotFulfillFromLocationCondition::class,
            'description' => 'Can NOT Fulfill Warehouse Code (0 for all)',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsCondition::class,
            'description' => 'Shipping Method Code equals',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\ShippingMethodCodeInCondition::class,
            'description' => 'Shipping Method Code in',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\LineCountEqualsCondition::class,
            'description' => 'Line count equals',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\TotalQuantityToShipEqualsCondition::class,
            'description' => 'Total Quantity To Ship',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\IsPartiallyPaidCondition::class,
            'description' => 'Is Partially Paid',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\IsFullyPaidCondition::class,
            'description' => 'Is Fully Paid',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\IsFullyPickedCondition::class,
            'description' => 'Is Fully Picked',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\IsFullyPackedCondition::class,
            'description' => 'Is Fully Packed',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\OrderNumberEqualsCondition::class,
            'description' => 'Order Number equals',
        ],
        [
            'class' => \App\Modules\Automations\src\Conditions\Order\HasTagsCondition::class,
            'description' => 'Has tags',
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
        [
            'class' => \App\Modules\Automations\src\Actions\SplitBundleSkuAction::class,
            'description' => 'Split bundle SKU (format: BundleSKU,SKU1,SKU2...)',
        ],
        [
            'class' => \App\Modules\Automations\src\Actions\Order\AttachTagsAction::class,
            'description' => 'Attach tags',
        ],
        [
            'class' => \App\Modules\Automations\src\Actions\Order\DetachTagsAction::class,
            'description' => 'Detach tags',
        ],
    ]
];
