<?php

namespace JustBetter\MagentoAsync\Tests\Actions;

use Illuminate\Support\Carbon;
use JustBetter\MagentoAsync\Actions\CleanBulkRequests;
use JustBetter\MagentoAsync\Enums\OperationStatus;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class CleanBulkRequestsTest extends TestCase
{
    #[Test]
    public function it_deletes_completed_operations(): void
    {
        /** @var BulkRequest $request */
        $request = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-1::',
            'request' => [],
            'response' => [],
            'created_at' => now(),
        ]);

        $request->operations()->create([
            'operation_id' => 1,
            'status' => OperationStatus::Complete,
        ]);

        $request->operations()->create([
            'operation_id' => 2,
            'status' => OperationStatus::Open,
        ]);

        /** @var CleanBulkRequests $action */
        $action = app(CleanBulkRequests::class);
        $action->clean();

        $this->assertEquals(1, $request->operations()->count());
    }

    /** @param array<string, mixed> $operations */
    #[Test]
    #[DataProvider('cases')]
    public function it_deletes_request(Carbon $requestCreatedAt, array $operations, bool $shouldBeDeleted): void
    {
        config()->set('magento-async.cleanup', 2);

        /** @var BulkRequest $request */
        $request = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-1::',
            'request' => [],
            'response' => [],
            'created_at' => $requestCreatedAt,
        ]);

        foreach ($operations as $operation) {
            $request->operations()->create($operation);
        }

        /** @var CleanBulkRequests $action */
        $action = app(CleanBulkRequests::class);
        $action->clean();

        $deleted = BulkRequest::query()->firstWhere('bulk_uuid', '=', '::bulk-uuid-1::') === null;
        $this->assertEquals($shouldBeDeleted, $deleted);
    }

    /** @return array<string, mixed> */
    public static function cases(): array
    {
        return [
            'Pending operation' => [
                'requestCreatedAt' => now(),
                'operations' => [
                    [
                        'operation_id' => 1,
                        'status' => null,
                    ],
                ],
                'shouldBeDeleted' => false,
            ],

            'Completed operations' => [
                'requestCreatedAt' => now()->subHours(2),
                'operations' => [
                    [
                        'operation_id' => 1,
                        'status' => OperationStatus::Complete,
                    ],
                ],
                'shouldBeDeleted' => true,
            ],

            'Failed operations' => [
                'requestCreatedAt' => now()->subHours(2),
                'operations' => [
                    [
                        'operation_id' => 1,
                        'status' => OperationStatus::RetriablyFailed,
                    ],
                ],
                'shouldBeDeleted' => false,
            ],

            'Cleanup time' => [
                'requestCreatedAt' => now()->subWeek(),
                'operations' => [
                    [
                        'operation_id' => 1,
                        'status' => OperationStatus::RetriablyFailed,
                    ],
                ],
                'shouldBeDeleted' => true,
            ],

        ];
    }
}
