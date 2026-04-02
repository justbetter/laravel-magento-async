<?php

declare(strict_types=1);

namespace JustBetter\MagentoAsync\Contracts;

use JustBetter\MagentoAsync\Models\BulkRequest;

interface UpdatesBulkStatus
{
    public function update(BulkRequest $bulkRequest): void;
}
