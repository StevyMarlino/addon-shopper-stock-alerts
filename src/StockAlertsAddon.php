<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts;

use Shopper\Addon\BaseAddon;
use Shopper\ShopperPanel;

final class StockAlertsAddon extends BaseAddon
{
    public function getId(): string
    {
        return 'stock-alerts';
    }

    public function register(ShopperPanel $panel): void
    {
        //
    }
}
