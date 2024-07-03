<?php

namespace JustBetter\MagentoAsync;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JustBetter\MagentoAsync\Actions\CleanBulkRequests;
use JustBetter\MagentoAsync\Actions\UpdateBulkStatus;
use JustBetter\MagentoAsync\Actions\UpdateBulkStatuses;
use JustBetter\MagentoAsync\Commands\CleanBulkRequestsCommand;
use JustBetter\MagentoAsync\Commands\UpdateBulkStatusCommand;
use JustBetter\MagentoAsync\Commands\UpdateBulkStatusesCommand;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this
            ->registerConfig()
            ->registerActions();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/magento-async.php', 'magento-async');

        return $this;
    }

    protected function registerActions(): static
    {
        UpdateBulkStatus::bind();
        UpdateBulkStatuses::bind();
        CleanBulkRequests::bind();

        return $this;
    }

    public function boot(): void
    {
        $this
            ->bootConfig()
            ->bootMigrations()
            ->bootCommands();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/magento-async.php' => config_path('magento-async.php'),
        ], 'config');

        return $this;
    }

    protected function bootMigrations(): static
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        return $this;
    }

    protected function bootCommands(): static
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                UpdateBulkStatusCommand::class,
                UpdateBulkStatusesCommand::class,
                CleanBulkRequestsCommand::class,
            ]);
        }

        return $this;
    }
}
