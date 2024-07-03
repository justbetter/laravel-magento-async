<?php

namespace JustBetter\MagentoAsync\Tests\Commands;

use Illuminate\Support\Facades\Bus;
use Illuminate\Testing\PendingCommand;
use JustBetter\MagentoAsync\Commands\UpdateBulkStatusesCommand;
use JustBetter\MagentoAsync\Jobs\UpdateBulkStatusesJob;
use JustBetter\MagentoAsync\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UpdateBulkStatusesCommandTest extends TestCase
{
    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Bus::fake();

        /** @var PendingCommand $artisan */
        $artisan = $this->artisan(UpdateBulkStatusesCommand::class);

        $artisan
            ->assertSuccessful()
            ->run();

        Bus::assertDispatched(UpdateBulkStatusesJob::class);
    }
}
