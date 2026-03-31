<?php

declare(strict_types=1);

namespace JustBetter\MagentoAsync\Contracts;

interface CleansBulkRequests
{
    public function clean(): void;
}
