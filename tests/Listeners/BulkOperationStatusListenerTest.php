<?php

namespace JustBetter\MagentoAsync\Tests\Listeners;

use JustBetter\MagentoAsync\Events\BulkOperationStatusEvent;
use JustBetter\MagentoAsync\Models\BulkOperation;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\Fakes\FakeBulkOperationStatusListener;
use JustBetter\MagentoAsync\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

class BulkOperationStatusListenerTest extends TestCase
{
    #[Test]
    public function it_can_handle_a_status_change(): void
    {
        /** @var BulkRequest $request */
        $request = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'method' => 'POST',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid::',
            'request' => [],
            'response' => [],
        ]);

        /** @var BulkOperation $operation */
        $operation = $request->operations()->create([
            'subject_id' => $request->getKey(),
            'subject_type' => $request->getMorphClass(),
            'operation_id' => 0,
        ]);

        $event = new BulkOperationStatusEvent($operation);

        $this->partialMock(FakeBulkOperationStatusListener::class, function (MockInterface $mock): void {
            $mock
                ->shouldReceive('execute')
                ->once();
        });

        /** @var FakeBulkOperationStatusListener $listener */
        $listener = app(FakeBulkOperationStatusListener::class);
        $listener->handle($event);
    }

    #[Test]
    public function it_can_skip_executing(): void
    {
        /** @var BulkRequest $request */
        $request = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'method' => 'POST',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid::',
            'request' => [],
            'response' => [],
        ]);

        /** @var BulkOperation $operation */
        $operation = $request->operations()->create([
            'operation_id' => 0,
        ]);

        $event = new BulkOperationStatusEvent($operation);

        $this->partialMock(FakeBulkOperationStatusListener::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('execute');
        });

        /** @var FakeBulkOperationStatusListener $listener */
        $listener = app(FakeBulkOperationStatusListener::class);
        $listener->handle($event);
    }
}
