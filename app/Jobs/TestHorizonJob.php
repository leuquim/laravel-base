<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TestHorizonJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $message = 'Hello from Horizon!'
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Simulate some work
        sleep(2);

        // Log the message
        logger()->info('TestHorizonJob executed: '.$this->message);
    }
}
