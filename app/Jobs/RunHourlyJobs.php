<?php

namespace App\Jobs;

use App\Events\HourlyEvent;
use App\Models\OrderAddress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class RunHourlyListener.
 */
class RunHourlyJobs implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Hourly event - dispatching');

        HourlyEvent::dispatch();

        Log::info('Hourly event - dispatched successfully');

        OrderAddress::query()
            ->whereNull('first_name_encrypted')
            ->orderBy('id')
            ->limit(10000)
            ->get()
            ->each(function (OrderAddress $address) {
                OrderAddress::query()->where(['id' => $address->getKey()])->toBase()->update([
                    'first_name' => '',
                    'first_name_encrypted' => \Crypt::encryptString($address->first_name),
                    'last_name' => '',
                    'last_name_encrypted' => \Crypt::encryptString($address->last_name),
                    'phone' => '',
                    'phone_encrypted' => \Crypt::encryptString($address->phone),
                    'email' => '',
                    'email_encrypted' => \Crypt::encryptString($address->email),
                ]);
            });
    }
}
