<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExampleJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $ifNeeded;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ifNeeded)
    {
        $this->ifNeeded = $ifNeeded;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
