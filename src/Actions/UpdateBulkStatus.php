<?php

namespace JustBetter\MagentoAsync\Actions;

use Illuminate\Support\Carbon;
use JustBetter\MagentoAsync\Contracts\UpdatesBulkStatus;
use JustBetter\MagentoAsync\Enums\OperationStatus;
use JustBetter\MagentoAsync\Events\BulkOperationStatusEvent;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoClient\Client\Magento;

class UpdateBulkStatus implements UpdatesBulkStatus
{
    public function __construct(
        protected Magento $magento
    ) {}

    public function update(BulkRequest $bulkRequest): void
    {
        $response = $this->magento
            ->get('bulk/'.$bulkRequest->bulk_uuid.'/status')
            ->throw();

        $startTime = $response->json('start_time');

        $startDate = $startTime !== null
            ? Carbon::parse($startTime)
            : null;

        $bulkRequest->started_at = $startDate;
        $bulkRequest->save();

        $bulkRequest->load('operations');

        /** @var array<int, array<string, mixed>> $operations */
        $operations = $response->json('operations_list');

        foreach ($operations as $operation) {
            $model = $bulkRequest->operations->where('operation_id', '=', $operation['id'])->first();

            if ($model === null) {
                continue;
            }

            $model->status = OperationStatus::tryFrom($operation['status']);
            $model->response = $operation;

            $statusChanged = $model->isDirty('status');

            $model->save();

            if ($statusChanged) {
                BulkOperationStatusEvent::dispatch($model);
            }
        }
    }

    public static function bind(): void
    {
        app()->singleton(UpdatesBulkStatus::class, static::class);
    }
}
