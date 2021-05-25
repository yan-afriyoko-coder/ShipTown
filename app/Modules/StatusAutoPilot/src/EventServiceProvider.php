<?php

namespace App\Modules\StatusAutoPilot\src;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\StatusAutoPilot\src\Listeners\OrderUpdatedEvent\ProcessingToPaidListener;
use App\Modules\StatusAutoPilot\src\Listeners\OrderUpdatedEvent\SetReadyWhenPacked;
use App\Modules\StatusAutoPilot\src\Listeners\OrderUpdatedEvent\UpdateClosedAt;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderUpdatedEvent::class => [
            UpdateClosedAt::class,
            ProcessingToPaidListener::class,
            SetReadyWhenPacked::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
