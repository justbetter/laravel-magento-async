<?php

namespace JustBetter\MagentoAsync\Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use JustBetter\MagentoAsync\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
            \JustBetter\MagentoClient\ServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
