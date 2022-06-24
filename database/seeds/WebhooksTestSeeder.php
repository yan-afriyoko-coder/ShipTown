<?php

use App\Modules\Webhooks\src\Models\WebhooksConfiguration;
use App\Modules\Webhooks\src\WebhooksServiceProviderBase;
use Illuminate\Database\Seeder;

class WebhooksTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('TEST_SNS_TOPIC_ARN') === null) {
            return;
        }

        WebhooksConfiguration::query()->updateOrCreate([], [
            'topic_arn' => env('TEST_SNS_TOPIC_ARN')
        ]);

        WebhooksServiceProviderBase::enableModule();
    }
}
