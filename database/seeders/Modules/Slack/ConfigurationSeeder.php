<?php

namespace Database\Seeders\Modules\Slack;

use App\Modules\Slack\src\Models\IncomingWebhook;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    public function run()
    {
        if (env('TEST_SLACK_INCOMING_WEBHOOK_URL')) {
            IncomingWebhook::query()->firstOrCreate([
                'webhook_url' => env('TEST_SLACK_INCOMING_WEBHOOK_URL'),
            ]);
        }
    }
}
