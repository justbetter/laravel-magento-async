<?php

namespace JustBetter\MagentoAsync\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JustBetter\MagentoAsync\Contracts\UpdatesBulkStatus;
use JustBetter\MagentoAsync\Models\BulkRequest;

class UpdateBulkStatusJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public BulkRequest $bulkRequest
    ) {
        $this->onQueue(config('magento-async.queue'));
    }

    public function handle(UpdatesBulkStatus $contract): void
    {
        $contract->update($this->bulkRequest);
    }

    public function uniqueId(): int
    {
        return $this->bulkRequest->id;
    }
}
