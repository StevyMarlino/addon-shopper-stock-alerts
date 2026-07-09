<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts;

use Illuminate\Support\ServiceProvider;
use Shopper\Core\Models\InventoryHistory;
use Shopper\ShopperPanel;
use Stevymarlino\AddonShopperStockAlerts\Observers\RestockObserver;

final class AddonShopperStockAlertsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/stock-alerts.php', 'stock-alerts');

        $this->callAfterResolving('shopper', function (ShopperPanel $panel): void {
            $panel->addon(new StockAlertsAddon());
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if (config('shopper.addons.stock-alerts', true) !== false) {
            InventoryHistory::observe(RestockObserver::class);

            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/stock-alerts.php' => config_path('stock-alerts.php'),
            ], 'shopper-config');
        }
    }
}
