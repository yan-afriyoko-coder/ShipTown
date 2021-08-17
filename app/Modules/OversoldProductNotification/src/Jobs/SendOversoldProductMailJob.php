<?php

namespace App\Modules\OversoldProductNotification\src\Jobs;

use App\Mail\OversoldProductMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Spatie\MailTemplates\TemplateMailable;

class SendOversoldProductMailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    protected string $to;

    protected array $data;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $to)
    {
        $this->to = $to;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = new OversoldProductMail($this->data);

        Mail::to($this->to)->send($mail);
        $this->queueData(['to' => $this->to, 'data' => $this->data]);
    }
}
