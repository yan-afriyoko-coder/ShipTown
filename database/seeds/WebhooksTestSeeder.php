<?php

use App\Modules\Webhooks\src\Models\WebhooksConfiguration;
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
        if (env('TEST_SNS_TOPIC_ARN')) {
            WebhooksConfiguration::query()->updateOrCreate([], [
                'topic_arn' => env('TEST_SNS_TOPIC_ARN')
            ]);
        }
    }
}
