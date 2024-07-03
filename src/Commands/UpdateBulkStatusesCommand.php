<?php

namespace JustBetter\MagentoAsync\Commands;

use Illuminate\Console\Command;
use JustBetter\MagentoAsync\Jobs\UpdateBulkStatusesJob;

class UpdateBulkStatusesCommand extends Command
{
    protected $signature = 'magento:async:bulk-statuses';

    protected $description = 'Update all bulk statuses';

    public function handle(): int
    {
        UpdateBulkStatusesJob::dispatch();

        return static::SUCCESS;
    }
}
