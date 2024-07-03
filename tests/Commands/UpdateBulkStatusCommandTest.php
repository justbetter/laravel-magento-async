<?php

namespace JustBetter\MagentoAsync\Tests\Commands;

use Illuminate\Support\Facades\Bus;
use Illuminate\Testing\PendingCommand;
use JustBetter\MagentoAsync\Commands\UpdateBulkStatusCommand;
use JustBetter\MagentoAsync\Jobs\UpdateBulkStatusJob;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UpdateBulkStatusCommandTest extends TestCase
{
    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Bus::fake();

        /** @var BulkRequest $bulkRequest */
        $bulkRequest = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid::',
            'request' => [],
            'response' => [],
        ]);

        /** @var PendingCommand $artisan */
        $artisan = $this->artisan(UpdateBulkStatusCommand::class, [
            'id' => $bulkRequest->id,
        ]);

        $artisan
            ->assertSuccessful()
            ->run();

        Bus::assertDispatched(UpdateBulkStatusJob::class);
    }
}
