<?php

namespace JustBetter\MagentoAsync\Tests\Commands;

use Illuminate\Testing\PendingCommand;
use JustBetter\MagentoAsync\Commands\RetryBulkRequestCommand;
use JustBetter\MagentoAsync\Contracts\RetriesBulkRequest;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

class RetryBulkRequestCommandTest extends TestCase
{
    #[Test]
    public function it_calls_action(): void
    {
        /* @var BulkRequest $request */
        $request = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'method' => 'POST',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-1::',
            'request' => [],
            'response' => [],
            'created_at' => now(),
        ]);

        $this->mock(RetriesBulkRequest::class, function (MockInterface $mock) use ($request): void {
            $mock->shouldReceive('retry')
                ->withArgs(function (BulkRequest $bulkRequest, bool $onlyFailed) use ($request) {
                    return $bulkRequest->id === $request->id && $onlyFailed;
                })->once()
                ->andReturn($request);
        });

        /** @var PendingCommand $result */
        $result = $this->artisan(RetryBulkRequestCommand::class, [
            'id' => $request->id,
            '--only-failed' => true,
        ]);

        $result->assertSuccessful();
    }

    #[Test]
    public function it_fails_without_retry(): void
    {
        /* @var BulkRequest $request */
        $request = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'method' => 'POST',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid-1::',
            'request' => [],
            'response' => [],
            'created_at' => now(),
        ]);

        $this->mock(RetriesBulkRequest::class, function (MockInterface $mock) use ($request): void {
            $mock->shouldReceive('retry')
                ->withArgs(function (BulkRequest $bulkRequest, bool $onlyFailed) use ($request) {
                    return $bulkRequest->id === $request->id && $onlyFailed;
                })->once()
                ->andReturnNull();
        });

        /** @var PendingCommand $result */
        $result = $this->artisan(RetryBulkRequestCommand::class, [
            'id' => $request->id,
            '--only-failed' => true,
        ]);

        $result->assertFailed();
    }
}
