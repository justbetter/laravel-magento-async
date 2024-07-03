<?php

namespace JustBetter\MagentoAsync\Tests\Jobs;

use JustBetter\MagentoAsync\Contracts\UpdatesBulkStatuses;
use JustBetter\MagentoAsync\Jobs\UpdateBulkStatusesJob;
use JustBetter\MagentoAsync\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

class UpdateBulkStatusesJobTest extends TestCase
{
    #[Test]
    public function it_can_update_bulk_statuses(): void
    {
        $this->mock(UpdatesBulkStatuses::class, function (MockInterface $mock): void {
            $mock
                ->shouldReceive('update')
                ->once()
                ->andReturn();
        });

        UpdateBulkStatusesJob::dispatch();
    }
}
