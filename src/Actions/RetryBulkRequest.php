<?php

namespace JustBetter\MagentoAsync\Actions;

use Illuminate\Database\Eloquent\Model;
use JustBetter\MagentoAsync\Client\MagentoAsync;
use JustBetter\MagentoAsync\Contracts\RetriesBulkRequest;
use JustBetter\MagentoAsync\Enums\OperationStatus;
use JustBetter\MagentoAsync\Models\BulkOperation;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoClient\Client\Magento;

class RetryBulkRequest implements RetriesBulkRequest
{
    public function __construct(protected MagentoAsync $client)
    {
    }

    public function retry(BulkRequest $bulkRequest, bool $onlyFailed): ?BulkRequest
    {
        /** @var array<int, mixed> $payload */
        $payload = [];
        /** @var array<int, Model> $subjects */
        $subjects = [];

        $operations = $bulkRequest->operations;

        foreach ($bulkRequest->request as $index => $request) {

            /** @var BulkOperation $operation */
            $operation = $operations->where('operation_id', '=', $index)->firstOrFail();

            if ($onlyFailed && ! in_array($operation->status, OperationStatus::failedStatuses())) {
                continue;
            }

            $payload[] = $request;
            if ($operation->subject !== null) {
                $subjects[] = $operation->subject;
            } else {
                $subjects[] = null;
            }
        }

        if ($payload === []) {
            return null;
        }

        $pendingRequest = $this->client
            ->configure(fn (Magento $client): Magento => $client->store($bulkRequest->store_code))
            ->subjects($subjects);

        $retry = match ($bulkRequest->method) {
            'POST' => $pendingRequest->postBulk($bulkRequest->path, $payload),
            'PUT' => $pendingRequest->putBulk($bulkRequest->path, $payload),
            'DELETE' => $pendingRequest->deleteBulk($bulkRequest->path, $payload),
            default => null,
        };

        if ($retry !== null) {
            $retry->update([
                'retry_of' => $bulkRequest->id,
            ]);
        }

        return $retry;
    }

    public static function bind(): void
    {
        app()->singleton(RetriesBulkRequest::class, static::class);
    }
}
