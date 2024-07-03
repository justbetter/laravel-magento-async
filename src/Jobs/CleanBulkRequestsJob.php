<?php

namespace JustBetter\MagentoAsync\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use JustBetter\MagentoAsync\Contracts\CleansBulkRequests;

class CleanBulkRequestsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct()
    {
        $this->onQueue(config('magento-async.queue'));
    }

    public function handle(CleansBulkRequests $contract): void
    {
        $contract->clean();
    }
}
