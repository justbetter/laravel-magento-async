<?php

declare(strict_types=1);

namespace JustBetter\MagentoAsync\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use JustBetter\MagentoAsync\Models\BulkOperation;
use JustBetter\MagentoAsync\Models\BulkRequest;
use JustBetter\MagentoAsync\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class BulkOperationTest extends TestCase
{
    #[Test]
    public function it_is_linked_to_a_request_and_can_have_a_subject(): void
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

        $this->assertInstanceOf(Model::class, $operation->subject);
        $this->assertInstanceOf(BulkRequest::class, $operation->request);
    }
}
