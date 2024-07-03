<?php

namespace JustBetter\MagentoAsync\Tests\Models;

use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BulkRequestTest extends TestCase
{
    #[Test]
    public function it_can_have_operations(): void
    {
        /** @var BulkRequest $request */
        $request = BulkRequest::query()->create([
            'magento_connection' => '::magento-connection::',
            'store_code' => '::store-code::',
            'path' => '::path::',
            'bulk_uuid' => '::bulk-uuid::',
            'request' => [],
            'response' => [],
        ]);

        $request->operations()->createMany([
            [
                'operation_id' => 0,
            ],
            [
                'operation_id' => 1,
            ],
            [
                'operation_id' => 2,
            ],
        ]);

        $this->assertCount(3, $request->operations);
    }
}
