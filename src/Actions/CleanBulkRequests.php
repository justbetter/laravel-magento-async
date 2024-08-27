<?php

namespace JustBetter\MagentoAsync\Actions;

use Illuminate\Database\Eloquent\Builder;
use JustBetter\MagentoAsync\Contracts\CleansBulkRequests;
use JustBetter\MagentoAsync\Enums\OperationStatus;
use JustBetter\MagentoAsync\Models\BulkRequest;

class CleanBulkRequests implements CleansBulkRequests
{
    public function clean(): void
    {
        /** @var int $cleanupHours */
        $cleanupHours = config('magento-async.cleanup');

        BulkRequest::query()
            // Delete where there are no more operations with a non-final status
            ->where(function (Builder $query) use ($cleanupHours): void {
                $query->whereDoesntHave('operations', function (Builder $query): void {
                    $query
                        ->whereNull('status')
                        ->orWhere('status', '=', OperationStatus::Open);
                })->orWhere('created_at', '<', now()->subHour()); // Never delete within an hour
            })
            // Always delete after 7 days
            ->orWhere('created_at', '<', now()->subHours($cleanupHours))
            ->delete();
    }

    public static function bind(): void
    {
        app()->singleton(CleansBulkRequests::class, static::class);
    }
}
