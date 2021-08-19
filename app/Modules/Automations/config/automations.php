<?php

return [
    'when' => [
        [
            'class' => \App\Events\Order\OrderCreatedEvent::class,
            'description' => 'Order is created',
            'validators' => [
                [
                    'class' => \App\Modules\Automations\src\Validators\Order\ShippingMethodCodeEqualsValidator::class,
                    'description' => 'Order Shipping Method Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Validators\Order\StatusCodeEqualsValidator::class,
                    'description' => 'Order Status Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Validators\Order\CanFulfillFromLocationValidator::class,
                    'description' => 'Can Fulfill from location',
                ],
                [
                    'class' => \App\Modules\Automations\src\Validators\Order\CanNotFulfillFromLocationValidator::class,
                    'description' => 'Can NOT Fulfill from location',
                ],
            ],
            'executors' => [
                [
                    'class' => \App\Modules\Automations\src\Executors\Order\SetStatusCodeExecutor::class,
                    'description' => 'Set Order Status Code to',
                ],
                [
                    'class' => \App\Modules\Automations\src\Executors\Order\LogMessageExecutor::class,
                    'description' => 'Log order message',
                ],
                [
                    'class' => \App\Modules\Automations\src\Validators\Order\LineCountEqualsValidator::class,
                    'description' => 'Line count equals',
                ],
            ]
        ],
        [
            'class' => \App\Events\Order\OrderUpdatedEvent::class,
            'description' => 'Order is updated',
            'validators' => [
                [
                    'class' => \App\Modules\Automations\src\Validators\Order\ShippingMethodCodeEqualsValidator::class,
                    'description' => 'Order Shipping Method Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Validators\Order\StatusCodeEqualsValidator::class,
                    'description' => 'Order Status Code equals',
                ],
                [
                    'class' => \App\Modules\Automations\src\Validators\Order\CanFulfillFromLocationValidator::class,
                    'description' => 'Can Fulfill from location',
                ],
                [
                    'class' => \App\Modules\Automations\src\Validators\Order\CanNotFulfillFromLocationValidator::class,
                    'description' => 'Can NOT Fulfill from location',
                ],
                [
                    'class' => \App\Modules\Automations\src\Validators\Order\LineCountEqualsValidator::class,
                    'description' => 'Line count equals',
                ],
            ],
            'executors' => [
                [
                    'class' => \App\Modules\Automations\src\Executors\Order\SetStatusCodeExecutor::class,
                    'description' => 'Set Order Status Code to',
                ],
                [
                    'class' => \App\Modules\Automations\src\Executors\Order\LogMessageExecutor::class,
                    'description' => 'Log order message',
                ],
            ]
        ],
    ],
];
