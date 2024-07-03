<?php

namespace JustBetter\MagentoAsync\Actions;

use JustBetter\MagentoAsync\Contracts\CleansBulkRequests;
use JustBetter\MagentoAsync\Models\BulkRequest;

class CleanBulkRequests implements CleansBulkRequests
{
    public function clean(): void
    {
        /** @var int $cleanupHours */
        $cleanupHours = config('magento-async.cleanup');

        BulkRequest::query()
            ->where('created_at', '<', now()->subHours($cleanupHours))
            ->delete();
    }

    public static function bind(): void
    {
        app()->singleton(CleansBulkRequests::class, static::class);
    }
}
