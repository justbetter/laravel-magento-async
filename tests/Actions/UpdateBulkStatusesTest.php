<?php

namespace JustBetter\MagentoAsync\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use JustBetter\MagentoAsync\Actions\UpdateBulkStatuses;
use JustBetter\MagentoAsync\Enums\OperationStatus;
use JustBetter\MagentoAsync\Jobs\UpdateBulkStatusJob;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UpdateBulkStatusesTest extends TestCase
{
    #[Test]
    public function it_can_update_bulk_statuses(): void
    {
        Bus::fake();

        /** @var BulkRequest $status1 */
        $status1 = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-1::',
            'request' => [],
            'response' => [],
        ]);

        /** @var BulkRequest $status2 */
        $status2 = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-2::',
            'request' => [],
            'response' => [],
        ]);

        /** @var BulkRequest $status3 */
        $status3 = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-3::',
            'request' => [],
            'response' => [],
        ]);

        $status1->operations()->create([
            'operation_id' => 1,
            'status' => OperationStatus::Open,
        ]);

        $status2->operations()->create([
            'operation_id' => 1,
            'status' => null,
        ]);

        $status3->operations()->create([
            'operation_id' => 1,
            'status' => OperationStatus::Complete,
        ]);

        /** @var UpdateBulkStatuses $action */
        $action = app(UpdateBulkStatuses::class);
        $action->update();

        Bus::assertDispatched(UpdateBulkStatusJob::class, 2);
    }
}
