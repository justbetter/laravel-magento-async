<?php

namespace JustBetter\MagentoAsync\Commands;

use Illuminate\Console\Command;
use JustBetter\MagentoAsync\Jobs\CleanBulkRequestsJob;

class CleanBulkRequestsCommand extends Command
{
    protected $signature = 'magento:async:clean-bulk-requests';

    protected $description = 'Cleanup bulk requests';

    public function handle(): int
    {
        CleanBulkRequestsJob::dispatch();

        return static::SUCCESS;
    }
}
