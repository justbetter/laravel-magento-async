<?php

namespace JustBetter\MagentoAsync\Tests\Commands;

use Illuminate\Support\Facades\Bus;
use JustBetter\MagentoAsync\Commands\CleanBulkRequestsCommand;
use JustBetter\MagentoAsync\Jobs\CleanBulkRequestsJob;
use JustBetter\MagentoAsync\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CleanBulkRequestsCommandTest extends TestCase
{
    #[Test]
    public function it_dispatches_job(): void
    {
        Bus::fake();

        $this->artisan(CleanBulkRequestsCommand::class);

        Bus::assertDispatched(CleanBulkRequestsJob::class);
    }
}
