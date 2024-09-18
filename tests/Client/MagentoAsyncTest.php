<?php

namespace JustBetter\MagentoAsync\Tests\Client;

use Illuminate\Support\Facades\Http;
use JustBetter\MagentoAsync\Client\MagentoAsync;
use JustBetter\MagentoAsync\Models\BulkOperation;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use JustBetter\MagentoClient\Client\Magento;
use PHPUnit\Framework\Attributes\Test;

class MagentoAsyncTest extends TestCase
{
    #[Test]
    public function it_can_process_responses(): void
    {
        Magento::fake();

        Http::fake([
            'magento/rest/test/async/V1/path' => Http::response([
                'bulk_uuid' => '4c51f869-0a77-4238-be2a-290dd5081666',
                'request_items' => [
                    [
                        'id' => 0,
                        'data_hash' => '4c51f8690a774238be2a290dd50816664c51f8690a774238be2a290dd5081666',
                        'status' => 'accepted',
                    ],
                ],
                'errors' => false,
            ]),
        ])->preventStrayRequests();

        /** @var MagentoAsync $client */
        $client = app(MagentoAsync::class);

        $subject = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'method' => 'POST',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid::',
            'request' => [],
            'response' => [],
        ]);

        $processed = 0;
        $accepted = 0;
        $rejected = 0;
        $completed = 0;

        $request = $client->configure(function (Magento $magento): void {
            $magento->store('test');
        })->onProcessed(function () use (&$processed): void {
            $processed++;
        })->onAccepted(function () use (&$accepted): void {
            $accepted++;
        })->onRejected(function () use (&$rejected): void {
            $rejected++;
        })->onCompleted(function () use (&$completed): void {
            $completed++;
        })
            ->subject($subject)
            ->post('path', []);

        $this->assertEquals(1, $processed);
        $this->assertEquals(1, $accepted);
        $this->assertEquals(0, $rejected);
        $this->assertEquals(1, $completed);

        $this->assertEquals(1, $request->operations()->count());

        /** @var BulkOperation $operation */
        $operation = $request->operations()->first();

        $this->assertNotNull($operation->subject);
    }

    #[Test]
    public function it_can_process_bulk_responses(): void
    {
        Magento::fake();

        Http::fake([
            'magento/rest/test/async/bulk/V1/path' => Http::response([
                'bulk_uuid' => '4c51f869-0a77-4238-be2a-290dd5081666',
                'request_items' => [
                    [
                        'id' => 0,
                        'data_hash' => '4c51f8690a774238be2a290dd50816664c51f8690a774238be2a290dd5081666',
                        'status' => 'accepted',
                    ],
                    [
                        'id' => 1,
                        'data_hash' => '4c51f8690a774238be2a290dd50816664c51f8690a774238be2a290dd5081666',
                        'status' => 'rejected',
                    ],
                ],
                'errors' => true,
            ]),
        ])->preventStrayRequests();

        /** @var MagentoAsync $client */
        $client = app(MagentoAsync::class);

        $subjects = [
            BulkRequest::query()->create([
                'magento_connection' => '::magento-connection::',
                'store_code' => '::store-code::',
                'method' => 'POST',
                'path' => '::path::',
                'bulk_uuid' => '::bulk-uuid-1::',
                'request' => [],
                'response' => [],
            ]),
            BulkRequest::query()->create([
                'magento_connection' => '::magento-connection::',
                'store_code' => '::store-code::',
                'method' => 'POST',
                'path' => '::path::',
                'bulk_uuid' => '::bulk-uuid-2::',
                'request' => [],
                'response' => [],
            ]),
        ];

        $processed = 0;
        $accepted = 0;
        $rejected = 0;
        $completed = 0;

        $request = $client->configure(function (Magento $magento): void {
            $magento->store('test');
        })->onProcessed(function () use (&$processed): void {
            $processed++;
        })->onAccepted(function () use (&$accepted): void {
            $accepted++;
        })->onRejected(function () use (&$rejected): void {
            $rejected++;
        })->onCompleted(function () use (&$completed): void {
            $completed++;
        })
            ->subjects($subjects)
            ->postBulk('path', []);

        $this->assertEquals(2, $processed);
        $this->assertEquals(1, $accepted);
        $this->assertEquals(1, $rejected);
        $this->assertEquals(1, $completed);

        $this->assertEquals(2, $request->operations()->count());
    }

    #[Test]
    public function it_can_make_put_calls(): void
    {
        Magento::fake();

        Http::fake([
            'magento/rest/all/async/V1/path' => Http::response([
                'bulk_uuid' => '4c51f869-0a77-4238-be2a-290dd5081666',
                'request_items' => [
                    [
                        'id' => 0,
                        'data_hash' => '4c51f8690a774238be2a290dd50816664c51f8690a774238be2a290dd5081666',
                        'status' => 'accepted',
                    ],
                ],
                'errors' => false,
            ]),
        ])->preventStrayRequests();

        /** @var MagentoAsync $client */
        $client = app(MagentoAsync::class);
        $response = $client->put('path', []);

        $this->assertEquals(1, $response->operations()->count());
    }

    #[Test]
    public function it_can_make_delete_calls(): void
    {
        Magento::fake();

        Http::fake([
            'magento/rest/all/async/V1/path' => Http::response([
                'bulk_uuid' => '4c51f869-0a77-4238-be2a-290dd5081666',
                'request_items' => [
                    [
                        'id' => 0,
                        'data_hash' => '4c51f8690a774238be2a290dd50816664c51f8690a774238be2a290dd5081666',
                        'status' => 'accepted',
                    ],
                ],
                'errors' => false,
            ]),
        ])->preventStrayRequests();

        /** @var MagentoAsync $client */
        $client = app(MagentoAsync::class);
        $response = $client->delete('path', []);

        $this->assertEquals(1, $response->operations()->count());
    }

    #[Test]
    public function it_can_make_bulk_put_calls(): void
    {
        Magento::fake();

        Http::fake([
            'magento/rest/all/async/bulk/V1/path' => Http::response([
                'bulk_uuid' => '4c51f869-0a77-4238-be2a-290dd5081666',
                'request_items' => [
                    [
                        'id' => 0,
                        'data_hash' => '4c51f8690a774238be2a290dd50816664c51f8690a774238be2a290dd5081666',
                        'status' => 'accepted',
                    ],
                ],
                'errors' => false,
            ]),
        ])->preventStrayRequests();

        /** @var MagentoAsync $client */
        $client = app(MagentoAsync::class);
        $response = $client->putBulk('path', []);

        $this->assertEquals(1, $response->operations()->count());
    }

    #[Test]
    public function it_can_make_bulk_delete_calls(): void
    {
        Magento::fake();

        Http::fake([
            'magento/rest/all/async/bulk/V1/path' => Http::response([
                'bulk_uuid' => '4c51f869-0a77-4238-be2a-290dd5081666',
                'request_items' => [
                    [
                        'id' => 0,
                        'data_hash' => '4c51f8690a774238be2a290dd50816664c51f8690a774238be2a290dd5081666',
                        'status' => 'accepted',
                    ],
                ],
                'errors' => false,
            ]),
        ])->preventStrayRequests();

        /** @var MagentoAsync $client */
        $client = app(MagentoAsync::class);
        $response = $client->deleteBulk('path', []);

        $this->assertEquals(1, $response->operations()->count());
    }
}
