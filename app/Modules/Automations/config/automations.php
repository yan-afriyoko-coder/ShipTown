<?php

return [
    'when' => [
        [
            'class' => \App\Events\Order\OrderCreatedEvent::class,
            'description' => 'Order is created',
            'conditions' => [
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsCondition::class,
                    'description' => 'Order Shipping Method Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition::class,
                    'description' => 'Order Status Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationCondition::class,
                    'description' => 'Can Fulfill from location',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\CanNotFulfillFromLocationCondition::class,
                    'description' => 'Can NOT Fulfill from location',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\LineCountEqualsCondition::class,
                    'description' => 'Line count equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\IsFullyPaidCondition::class,
                    'description' => 'Is Fully Paid',
                ],
            ],
            'actions' => [
                [
                    'class' => \App\Modules\Automations\src\Actions\Order\SetStatusCodeAction::class,
                    'description' => 'Set Order Status Code to',
                ],
                [
                    'class' => \App\Modules\Automations\src\Actions\Order\LogMessageAction::class,
                    'description' => 'Log order message',
                ],
            ]
        ],
        [
            'class' => \App\Events\Order\OrderUpdatedEvent::class,
            'description' => 'Order is updated',
            'conditions' => [
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsCondition::class,
                    'description' => 'Order Shipping Method Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition::class,
                    'description' => 'Order Status Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationCondition::class,
                    'description' => 'Can Fulfill from location',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\CanNotFulfillFromLocationCondition::class,
                    'description' => 'Can NOT Fulfill from location',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\LineCountEqualsCondition::class,
                    'description' => 'Line count equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\IsFullyPaidCondition::class,
                    'description' => 'Is Fully Paid',
                ],
            ],
            'actions' => [
                [
                    'class' => \App\Modules\Automations\src\Actions\Order\SetStatusCodeAction::class,
                    'description' => 'Set Order Status Code to',
                ],
                [
                    'class' => \App\Modules\Automations\src\Actions\Order\LogMessageAction::class,
                    'description' => 'Log order message',
                ],
            ]
        ],
        [
            'class' => \App\Events\Order\ActiveOrderCheckEvent::class,
            'description' => 'On Order Check Event',
            'conditions' => [
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsCondition::class,
                    'description' => 'Order Shipping Method Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition::class,
                    'description' => 'Order Status Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\StatusCodeNotInCondition::class,
                    'description' => 'Order Status Code Not In',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationCondition::class,
                    'description' => 'Can Fulfill from location (0 for all)',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\CanNotFulfillFromLocationCondition::class,
                    'description' => 'Can NOT Fulfill from location (0 for all)',
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
                    'class' => \App\Modules\Automations\src\Conditions\Order\IsFullyPaidCondition::class,
                    'description' => 'Is Fully Paid',
                ],
                [
                    'class' => \App\Modules\Automations\src\Conditions\Order\IsFullyPackedCondition::class,
                    'description' => 'Is Fully Packed',
                ],
            ],
            'actions' => [
                [
                    'class' => \App\Modules\Automations\src\Actions\Order\SetStatusCodeAction::class,
                    'description' => 'Set Order Status Code to',
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
                    'class' => \App\Modules\Automations\src\Actions\Order\SplitOrderToWarehouseCodeAction::class,
                    'description' => 'Split Order to warehouse code',
                ],
            ]
        ],
    ],
];
