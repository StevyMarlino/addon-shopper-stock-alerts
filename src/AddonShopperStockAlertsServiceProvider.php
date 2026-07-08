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
        $this->callAfterResolving('shopper', function (ShopperPanel $panel): void {
            $panel->addon(new StockAlertsAddon());
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if (config('shopper.addons.stock-alerts', true) !== false) {
            InventoryHistory::observe(RestockObserver::class);
        }
    }
}
