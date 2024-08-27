<?php

namespace JustBetter\MagentoAsync\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\PendingDispatch;
use JustBetter\MagentoAsync\Contracts\UpdatesBulkStatuses;
use JustBetter\MagentoAsync\Enums\OperationStatus;
use JustBetter\MagentoAsync\Jobs\UpdateBulkStatusJob;
use JustBetter\MagentoAsync\Models\BulkRequest;

class UpdateBulkStatuses implements UpdatesBulkStatuses
{
    public function update(): void
    {
        BulkRequest::query()
            ->whereHas('operations', function (Builder $query): void {
                $query
                    ->where('status', '=', OperationStatus::Open)
                    ->orWhereNull('status');
            })
            ->get()
            ->each(fn (BulkRequest $bulkRequest): PendingDispatch => UpdateBulkStatusJob::dispatch($bulkRequest));
    }

    public static function bind(): void
    {
        app()->singleton(UpdatesBulkStatuses::class, static::class);
    }
}
