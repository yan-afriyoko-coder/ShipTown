<?php

namespace App\Modules\Slack\src\Automations;

use App\Modules\Automations\src\Abstracts\BaseOrderActionAbstract;
use App\Modules\Slack\src\Models\IncomingWebhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSlackNotificationAction extends BaseOrderActionAbstract
{
    /**
     * @param string $options
     * @return bool
     */
    public function handle(string $options = ''): bool
    {
        Log::debug('Automation Action', [
            'order_number' => $this->order->order_number,
            'class' => class_basename(self::class),
            'comment' => $options,
        ]);

        /** @var IncomingWebhook $incomingWebhook */
        $incomingWebhook = IncomingWebhook::query()->first();

        Http::post($incomingWebhook->webhook_url, [
            'text' => $this->order->order_number. ': ' . $options,
        ]);

        return true;
    }
}
