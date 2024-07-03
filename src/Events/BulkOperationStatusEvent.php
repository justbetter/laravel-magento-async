<?php

namespace JustBetter\MagentoAsync\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JustBetter\MagentoAsync\Models\BulkOperation;

class BulkOperationStatusEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public BulkOperation $bulkOperation
    ) {}
}
