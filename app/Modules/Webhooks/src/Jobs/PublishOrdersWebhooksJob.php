<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Http\Controllers\SnsController;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Modules\Webhooks\src\AwsSns;
use Aws\Exception\AwsException;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class PublishOrdersWebhooksJob
 * @package App\Modules\Webhooks\src\Jobs
 */
class PublishOrdersWebhooksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $awaiting_publish_tag = config('webhooks.tag.awaiting.name');

        $orders = Order::withAllTags($awaiting_publish_tag)
            ->with('orderProducts')
            ->get();

        $this->queueData(['orders_count' => $orders->count()]);

        $orders->each(function (Order $order) {
            $order->attachTag(config('webhooks.tags.publishing.name'));
            $order->detachTag(config('webhooks.tags.awaiting.name'));

            $orderResource = new OrderResource($order);
            if (! AwsSns::publish('orders_events', $orderResource->toJson())) {
                $order->attachTag(config('webhooks.tags.awaiting.name'));
            }

            $order->detachTag(config('webhooks.tags.publishing.name'));
        });
    }
}
