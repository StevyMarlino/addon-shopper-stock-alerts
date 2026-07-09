<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Shopper\Core\CoreServiceProvider;
use Shopper\Payment\PaymentServiceProvider;
use Shopper\ShopperServiceProvider;
use Shopper\Sidebar\SidebarServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
use Stevymarlino\AddonShopperStockAlerts\AddonShopperStockAlertsServiceProvider;
use Stevymarlino\AddonShopperStockAlerts\Tests\Stubs\User;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            CoreServiceProvider::class,
            ShopperServiceProvider::class,
            SidebarServiceProvider::class,
            MediaLibraryServiceProvider::class,
            PaymentServiceProvider::class,
            PermissionServiceProvider::class,
            AddonShopperStockAlertsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../vendor/shopper/core/database/migrations');
    }
}