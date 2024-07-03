<?php

namespace JustBetter\MagentoAsync\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use JustBetter\MagentoAsync\Actions\UpdateBulkStatuses;
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

        BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-1::',
            'request' => [],
            'response' => [],
        ]);

        BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-2::',
            'request' => [],
            'response' => [],
        ]);

        BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-3::',
            'request' => [],
            'response' => [],
        ]);

        /** @var UpdateBulkStatuses $action */
        $action = app(UpdateBulkStatuses::class);
        $action->update();

        Bus::assertDispatched(UpdateBulkStatusJob::class, 3);
    }
}
