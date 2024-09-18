<?php

namespace JustBetter\MagentoAsync\Tests\Jobs;

use JustBetter\MagentoAsync\Contracts\UpdatesBulkStatus;
use JustBetter\MagentoAsync\Jobs\UpdateBulkStatusJob;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

class UpdateBulkStatusJobTest extends TestCase
{
    #[Test]
    public function it_can_update_bulk_statuses(): void
    {
        $this->mock(UpdatesBulkStatus::class, function (MockInterface $mock): void {
            $mock
                ->shouldReceive('update')
                ->once()
                ->andReturn();
        });

        /** @var BulkRequest $bulkRequest */
        $bulkRequest = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'method' => 'POST',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid::',
            'request' => [],
            'response' => [],
        ]);

        UpdateBulkStatusJob::dispatch($bulkRequest);
    }
}
