<?php

namespace JustBetter\MagentoAsync\Tests\Actions;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use JustBetter\MagentoAsync\Actions\UpdateBulkStatus;
use JustBetter\MagentoAsync\Events\BulkOperationStatusEvent;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use JustBetter\MagentoClient\Client\Magento;
use PHPUnit\Framework\Attributes\Test;

class UpdateBulkStatusTest extends TestCase
{
    #[Test]
    public function it_can_update_bulk_statuses(): void
    {
        Event::fake();
        Magento::fake();

        Http::fake([
            'magento/rest/all/V1/bulk/4c51f869-0a77-4238-be2a-290dd5081666/status' => Http::response([
                'operations_list' => [
                    [
                        'id' => 0,
                        'status' => 1,
                        'result_message' => '::result-message::',
                        'error_code' => null,
                    ],
                ],
                'user_type' => 1,
                'bulk_id' => '4c51f869-0a77-4238-be2a-290dd5081666',
                'description' => '::description::',
                'start_time' => '2024-01-01 09:00:00',
                'user_id' => 1,
                'operation_count' => 1,
            ]),
        ])->preventStrayRequests();

        /** @var BulkRequest $bulkRequest */
        $bulkRequest = BulkRequest::query()->create([
            'magento_connection' => 'default',
            'store_code' => 'all',
            'path' => 'products',
            'bulk_uuid' => '4c51f869-0a77-4238-be2a-290dd5081666',
            'request' => [],
            'response' => [],
        ]);

        $bulkRequest->operations()->create([
            'operation_id' => 0,
        ]);

        /** @var UpdateBulkStatus $action */
        $action = app(UpdateBulkStatus::class);
        $action->update($bulkRequest);

        $this->assertNotNull($bulkRequest->started_at);

        Event::assertDispatched(BulkOperationStatusEvent::class);
    }

    #[Test]
    public function it_can_skip_the_started_at(): void
    {
        Event::fake();
        Magento::fake();

        Http::fake([
            'magento/rest/all/V1/bulk/4c51f869-0a77-4238-be2a-290dd5081666/status' => Http::response([
                'operations_list' => [
                    [
                        'id' => 0,
                        'status' => 1,
                        'result_message' => '::result-message::',
                        'error_code' => null,
                    ],
                    [
                        'id' => 1,
                        'status' => 1,
                        'result_message' => '::result-message::',
                        'error_code' => null,
                    ],
                ],
                'user_type' => 1,
                'bulk_id' => '4c51f869-0a77-4238-be2a-290dd5081666',
                'description' => '::description::',
                'start_time' => null,
                'user_id' => 1,
                'operation_count' => 2,
            ]),
        ])->preventStrayRequests();

        /** @var BulkRequest $bulkRequest */
        $bulkRequest = BulkRequest::query()->create([
            'magento_connection' => 'default',
            'store_code' => 'all',
            'path' => 'products',
            'bulk_uuid' => '4c51f869-0a77-4238-be2a-290dd5081666',
            'request' => [],
            'response' => [],
        ]);

        $bulkRequest->operations()->create([
            'operation_id' => 0,
        ]);

        /** @var UpdateBulkStatus $action */
        $action = app(UpdateBulkStatus::class);
        $action->update($bulkRequest);

        $this->assertNull($bulkRequest->started_at);

        Event::assertDispatched(BulkOperationStatusEvent::class);
    }
}
