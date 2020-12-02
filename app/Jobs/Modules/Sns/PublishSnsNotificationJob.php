<?php

namespace App\Jobs\Modules\Sns;

use App\Http\Controllers\SnsController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishSnsNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $topic;

    /**
     * @var string
     */
    private $message;

    /**
     * Create a new job instance.
     *
     * @param string $topic
     * @param string $message
     */
    public function __construct(string $topic, string $message)
    {
        $this->topic = $topic;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $snsTopic = new SnsController($this->topic);

        $snsTopic->publish($this->message);
    }
}
