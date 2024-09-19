<?php

namespace JustBetter\MagentoAsync\Commands;

use Illuminate\Console\Command;
use JustBetter\MagentoAsync\Contracts\RetriesBulkRequest;
use JustBetter\MagentoAsync\Models\BulkRequest;

class RetryBulkRequestCommand extends Command
{
    protected $signature = 'magento:async:retry-bulk-request {id} {--only-failed=true}';

    protected $description = 'Retry bulk request';

    public function handle(RetriesBulkRequest $contract): int
    {
        /** @var int $id */
        $id = $this->argument('id');

        /** @var bool $onlyFailed */
        $onlyFailed = $this->option('only-failed');

        /** @var BulkRequest $request */
        $request = BulkRequest::query()->findOrFail($id);

        $bulkRequest = $contract->retry($request, $onlyFailed);

        if ($bulkRequest === null) {
            $this->error('Failed to retry bulk request');

            return static::FAILURE;
        }

        $this->info('Retried with bulk uuid "'.$bulkRequest->bulk_uuid.'"');

        return static::SUCCESS;
    }
}
