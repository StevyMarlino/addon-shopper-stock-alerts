<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts;

use Shopper\Addon\BaseAddon;
use Shopper\ShopperPanel;
use Stevymarlino\AddonShopperStockAlerts\Sidebar\StockAlertsSidebar;

final class StockAlertsAddon extends BaseAddon
{
    public function getId(): string
    {
        return 'stock-alerts';
    }

    public function register(ShopperPanel $panel): void
    {
        /** @var class-string $index */
        $index = config('stock-alerts.components.subscription-index');

        $panel
            ->addonRoutes(fn () => require __DIR__.'/../routes/cpanel.php')
            ->addonViews('stock-alerts', __DIR__.'/../resources/views')
            ->addonLivewireComponents([
                'stock-alerts.subscription-index' => $index,
            ])
            ->addonSidebar(StockAlertsSidebar::class);
    }
}
