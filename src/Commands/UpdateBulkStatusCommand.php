<?php

namespace JustBetter\MagentoAsync\Commands;

use Illuminate\Console\Command;
use JustBetter\MagentoAsync\Jobs\UpdateBulkStatusJob;
use JustBetter\MagentoAsync\Models\BulkRequest;

class UpdateBulkStatusCommand extends Command
{
    protected $signature = 'magento:async:bulk-status {id}';

    protected $description = 'Update a single bulk status';

    public function handle(): int
    {
        /** @var int $id */
        $id = $this->argument('id');

        /** @var BulkRequest $request */
        $request = BulkRequest::query()->findOrFail($id);

        UpdateBulkStatusJob::dispatch($request);

        return static::SUCCESS;
    }
}
