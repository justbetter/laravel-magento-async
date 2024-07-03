<?php

namespace JustBetter\MagentoAsync\Tests\Fakes;

use JustBetter\MagentoAsync\Listeners\BulkOperationStatusListener;
use JustBetter\MagentoAsync\Models\BulkOperation;
use JustBetter\MagentoAsync\Models\BulkRequest;

class FakeBulkOperationStatusListener extends BulkOperationStatusListener
{
    protected string $model = BulkRequest::class;

    public function execute(BulkOperation $operation): void
    {
        //
    }
}
