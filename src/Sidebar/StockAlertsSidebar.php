<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Sidebar;

use Shopper\Sidebar\AbstractAdminSidebar;
use Shopper\Sidebar\Contracts\Builder\Group;
use Shopper\Sidebar\Contracts\Builder\Item;
use Shopper\Sidebar\Contracts\Builder\Menu;

final class StockAlertsSidebar extends AbstractAdminSidebar
{
    public function extendWith(Menu $menu): Menu
    {
        $menu->group(__('Stock Alerts'), function (Group $group): void {
            $group->weight(10);
            $group->setAuthorized();

            $group->item(__('Subscriptions'), function (Item $item): void {
                $item->setAuthorized();
                $item->useSpa();
                $item->route('shopper.stock-alerts.subscriptions.index');
                $item->setIcon('phosphor-bell');
            });
        });

        return $menu;
    }
}
