<?php

namespace App\Modules\AmazonSns\src\Jobs;

use App\Http\Controllers\SnsController;
use App\Http\Resources\OrderResource;
use App\Models\Order;
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
 * @package App\Modules\AmazonSns\src\Jobs
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

        Order::withAllTags($awaiting_publish_tag)
            ->with('orderProducts')
            ->get()
            ->each(function (Order $order) {
                $order->attachTag(config('webhooks.tags.publishing.name'));
                $order->detachTag(config('webhooks.tags.awaiting.name'));

                $orderResource = new OrderResource($order);
                if (! $this->publishWebhook('orders_events', $orderResource->toJson())) {
                    $order->attachTag(config('webhooks.tags.awaiting.name'));
                }

                $order->detachTag(config('webhooks.tags.publishing.name'));
            });
    }

    /**
     * @param string $topic
     * @param string $message
     * @return bool
     */
    private function publishWebhook(string $topic, string $message): bool
    {
        $snsTopic = new SnsController($topic);

        try {
            return $snsTopic->publish($message);
        } catch (AwsException $e) {
            Log::error("Could not publish SNS message", [
                "code" => $e->getStatusCode(),
                "return_message" => $e->getMessage(),
                "topic" => $topic,
                "message" => $message,
            ]);
            return false;
        } catch (Exception $e) {
            Log::error("Could not publish SNS message", [
                "code" => $e->getCode(),
                "return_message" => $e->getMessage(),
                "topic" => $topic,
                "message" => $message,
            ]);
            return false;
        }
    }
}
