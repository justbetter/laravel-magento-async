<?php

namespace JustBetter\MagentoAsync\Tests\Jobs;

use JustBetter\MagentoAsync\Contracts\CleansBulkRequests;
use JustBetter\MagentoAsync\Jobs\CleanBulkRequestsJob;
use JustBetter\MagentoAsync\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

class CleanBulkRequestsJobTest extends TestCase
{
    #[Test]
    public function it_calls_action(): void
    {
        $this->mock(CleansBulkRequests::class, function (MockInterface $mock): void {
            $mock->shouldReceive('clean')->once();
        });

        CleanBulkRequestsJob::dispatch();
    }
}
