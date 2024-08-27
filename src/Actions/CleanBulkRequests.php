<?php

namespace JustBetter\MagentoAsync\Actions;

use Illuminate\Database\Eloquent\Builder;
use JustBetter\MagentoAsync\Contracts\CleansBulkRequests;
use JustBetter\MagentoAsync\Enums\OperationStatus;
use JustBetter\MagentoAsync\Models\BulkOperation;
use JustBetter\MagentoAsync\Models\BulkRequest;

class CleanBulkRequests implements CleansBulkRequests
{
    public function clean(): void
    {
        /** @var int $cleanupHours */
        $cleanupHours = config('magento-async.cleanup');

        // Delete completed statuses
        BulkOperation::query()
            ->where('status', '=', OperationStatus::Complete)
            ->delete();

        BulkRequest::query()
            // Delete where there are no more operations
            ->where(function (Builder $query): void {
                $query
                    ->whereDoesntHave('operations')
                    ->where('created_at', '<', now()->subHour()); // Never delete within an hour
            })
            // Always delete after configured cleanup time
            ->orWhere('created_at', '<', now()->subHours($cleanupHours))
            ->delete();
    }

    public static function bind(): void
    {
        app()->singleton(CleansBulkRequests::class, static::class);
    }
}
