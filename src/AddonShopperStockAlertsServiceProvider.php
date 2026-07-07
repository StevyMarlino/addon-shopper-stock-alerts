<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts;

use Illuminate\Support\ServiceProvider;
use Shopper\ShopperPanel;

final class AddonShopperStockAlertsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->callAfterResolving('shopper', function (ShopperPanel $panel): void {
            $panel->addon(new StockAlertsAddon());
        });
    }
}