<?php

namespace JustBetter\MagentoAsync\Listeners;

use Illuminate\Database\Eloquent\Model;
use JustBetter\MagentoAsync\Enums\OperationStatus;
use JustBetter\MagentoAsync\Events\BulkOperationStatusEvent;
use JustBetter\MagentoAsync\Models\BulkOperation;

abstract class BulkOperationStatusListener
{
    protected string $model = Model::class;

    /** @var array<int, OperationStatus> */
    protected array $ignore = [
        OperationStatus::Open,
    ];

    public function handle(BulkOperationStatusEvent $event): void
    {
        $operation = $event->bulkOperation;

        /** @var Model $model */
        $model = app($this->model);

        if (
            $operation->subject_type !== $model->getMorphClass() ||
            in_array($operation->status, $this->ignore) ||
            $operation->subject === null
        ) {
            return;
        }

        $this->execute($operation);
    }

    abstract public function execute(BulkOperation $operation): void;
}
