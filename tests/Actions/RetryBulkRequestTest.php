<?php

namespace JustBetter\MagentoAsync\Tests\Actions;

use Illuminate\Support\Facades\Http;
use JustBetter\MagentoAsync\Actions\RetryBulkRequest;
use JustBetter\MagentoAsync\Enums\OperationStatus;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use JustBetter\MagentoClient\Client\Magento;
use PHPUnit\Framework\Attributes\Test;

class RetryBulkRequestTest extends TestCase
{
    #[Test]
    public function it_retries_bulk_request(): void
    {
        Magento::fake();

        Http::fake([
            'magento/rest/::store-code::/async/bulk/V1/::path::' => Http::response([
                'bulk_uuid' => 'new-uuid',
            ]),
        ])->preventStrayRequests();

        foreach (['POST', 'PUT', 'DELETE'] as $method) {
            /** @var BulkRequest $request */
            $request = BulkRequest::query()->create([
                'magento_connection' => '::magento-connection::',
                'store_code' => '::store-code::',
                'method' => $method,
                'path' => '::path::',
                'bulk_uuid' => '::bulk-uuid-1::',
                'request' => [
                    [
                        'call-1',
                    ],
                    [
                        'call-2',
                    ],
                    [
                        'call-3',
                    ],
                ],
                'response' => [],
                'created_at' => now(),
            ]);

            $request->operations()->create([
                'operation_id' => 0,
                'status' => OperationStatus::Complete,
                'updated_at' => now()->subHours(2),
            ]);

            $request->operations()->create([
                'operation_id' => 1,
                'status' => OperationStatus::RetriablyFailed,
                'subject_id' => $request->id,
                'subject_type' => get_class($request),
            ]);

            $request->operations()->create([
                'operation_id' => 2,
                'status' => OperationStatus::Open,
            ]);

            /** @var RetryBulkRequest $action */
            $action = app(RetryBulkRequest::class);

            $retry = $action->retry($request, true);

            $this->assertNotNull($retry);
            $this->assertEquals('::path::', $retry->path);
            $this->assertEquals($method, $retry->method);
            $this->assertEquals([['call-2']], $retry->request);
            $this->assertEquals($request->id, $retry->retry_of);
        }
    }

    #[Test]
    public function it_does_nothing_without_payload(): void
    {
        Http::fake()->preventStrayRequests();

        /** @var BulkRequest $request */
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

        /** @var RetryBulkRequest $action */
        $action = app(RetryBulkRequest::class);

        $action->retry($request, false);

        Http::assertNothingSent();
    }
}
