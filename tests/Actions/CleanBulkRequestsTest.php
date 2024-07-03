<?php

namespace JustBetter\MagentoAsync\Tests\Actions;

use JustBetter\MagentoAsync\Actions\CleanBulkRequests;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CleanBulkRequestsTest extends TestCase
{
    #[Test]
    public function it_deletes_request(): void
    {
        config()->set('magento-async.cleanup', 1);

        BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-1::',
            'request' => [],
            'response' => [],
            'created_at' => now(),
        ]);

        BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-2::',
            'request' => [],
            'response' => [],
            'created_at' => now()->subHours(2),
        ]);

        /** @var CleanBulkRequests $action */
        $action = app(CleanBulkRequests::class);
        $action->clean();

        $this->assertNotNull(BulkRequest::query()->firstWhere('bulk_uuid', '=', '::bulk-uuid-1::'));
        $this->assertNull(BulkRequest::query()->firstWhere('bulk_uuid', '=', '::bulk-uuid-2::'));

    }
}
