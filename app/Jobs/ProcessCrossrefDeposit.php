<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessCrossrefDeposit implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public \App\Models\Manuscript $manuscript
    ) {}

    /**
     * Execute the job.
     */
    public function handle(\App\Services\CrossrefService $service): void
    {
        $service->registerDoi($this->manuscript);
    }
}
