<?php

declare(strict_types=1);

namespace JustBetter\MagentoAsync\Contracts;

use JustBetter\MagentoAsync\Models\BulkRequest;

interface RetriesBulkRequest
{
    public function retry(BulkRequest $bulkRequest, bool $onlyFailed): ?BulkRequest;
}
