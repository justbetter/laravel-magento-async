<?php

namespace JustBetter\MagentoAsync\Client;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use JustBetter\MagentoAsync\Enums\ItemStatus;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoClient\Client\Magento;

class MagentoAsync
{
    protected ?Closure $onProcessed = null;

    protected ?Closure $onRejected = null;

    protected ?Closure $onAccepted = null;

    protected ?Closure $onCompleted = null;

    protected ?Model $subject = null;

    /** @var array<int, Model> */
    protected array $subjects = [];

    public function __construct(
        protected Magento $magento
    ) {}

    public function configure(Closure $closure): static
    {
        call_user_func($closure, $this->magento);

        return $this;
    }

    public function onProcessed(Closure $onProcessed): static
    {
        $this->onProcessed = $onProcessed;

        return $this;
    }

    public function onRejected(Closure $onRejected): static
    {
        $this->onRejected = $onRejected;

        return $this;
    }

    public function onAccepted(Closure $onAccepted): static
    {
        $this->onAccepted = $onAccepted;

        return $this;
    }

    public function onCompleted(Closure $onCompleted): static
    {
        $this->onCompleted = $onCompleted;

        return $this;
    }

    public function subject(Model $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /** @param array<int, Model> $subjects */
    public function subjects(array $subjects): static
    {
        $this->subjects = $subjects;

        return $this;
    }

    /** @param array<mixed, mixed> $data */
    public function post(string $path, array $data = []): BulkRequest
    {
        $response = $this->magento->postAsync($path, $data);

        return $this->processResponse($response, $path, $data);
    }

    /** @param array<mixed, mixed> $data */
    public function postBulk(string $path, array $data = []): BulkRequest
    {
        $response = $this->magento->postBulk($path, $data);

        return $this->processResponse($response, $path, $data, true);
    }

    /** @param array<mixed, mixed> $data */
    public function put(string $path, array $data = []): BulkRequest
    {
        $response = $this->magento->putAsync($path, $data);

        return $this->processResponse($response, $path, $data);
    }

    /** @param array<mixed, mixed> $data */
    public function putBulk(string $path, array $data = []): BulkRequest
    {
        $response = $this->magento->putBulk($path, $data);

        return $this->processResponse($response, $path, $data, true);
    }

    /** @param array<mixed, mixed> $data */
    public function delete(string $path, array $data = []): BulkRequest
    {
        $response = $this->magento->deleteAsync($path, $data);

        return $this->processResponse($response, $path, $data);
    }

    /** @param array<mixed, mixed> $data */
    public function deleteBulk(string $path, array $data = []): BulkRequest
    {
        $response = $this->magento->deleteBulk($path, $data);

        return $this->processResponse($response, $path, $data, true);
    }

    /** @param array<mixed, mixed> $data */
    public function processResponse(
        Response $response,
        string $path,
        array $data = [],
        bool $bulk = false
    ): BulkRequest {
        $response->throw();

        /** @var string $bulkUuid */
        $bulkUuid = $response->json('bulk_uuid');

        /** @var array<int, array<string, mixed>> $requestItems */
        $requestItems = $response->json('request_items', []);

        $response = $response->json(null, []);

        /** @var BulkRequest $bulkRequest */
        $bulkRequest = BulkRequest::query()->create([
            'magento_connection' => $this->magento->connection,
            'store_code' => $this->magento->storeCode ?? 'all',
            'path' => $path,
            'bulk_uuid' => $bulkUuid,
            'request' => $data,
            'response' => $response,
        ]);

        foreach ($requestItems as $index => $requestItem) {
            $payload = [
                'operation_id' => $requestItem['id'],
            ];

            $subject = $bulk
                ? $this->subjects[$index] ?? null
                : $this->subject;

            if ($subject !== null) {
                $payload = array_merge($payload, [
                    'subject_type' => $subject->getMorphClass(),
                    'subject_id' => $subject->getKey(),
                ]);
            }

            $bulkRequest->operations()->create($payload);

            $parameters = [
                $subject,
            ];

            if ($this->onProcessed !== null) {
                call_user_func($this->onProcessed, $parameters);
            }

            $status = ItemStatus::tryFrom($requestItem['status']);

            if ($status === ItemStatus::Accepted && $this->onAccepted !== null) {
                call_user_func($this->onAccepted, $parameters);
            }

            if ($status === ItemStatus::Rejected && $this->onRejected !== null) {
                call_user_func($this->onRejected, $parameters);
            }
        }

        if ($this->onCompleted !== null) {
            call_user_func($this->onCompleted, $bulkRequest);
        }

        return $bulkRequest;
    }
}
