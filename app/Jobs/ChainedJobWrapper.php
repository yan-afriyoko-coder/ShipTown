<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ChainedJobWrapper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /*
     * @var Cart $cart
     */
    private $cart;

    /**
     * Create a new job instance.
     *
     * @param
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * This job does not do anything, it acts as a wrapper for chained jobs.
     *
     * @return void
     */
    public function handle()
    {
    }
}