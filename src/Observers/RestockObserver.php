<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Observers;

use Shopper\Core\Models\InventoryHistory;
use Shopper\Core\Models\Product;
use Shopper\Core\Models\ProductVariant;
use Stevymarlino\AddonShopperStockAlerts\Jobs\NotifyStockSubscribers;

class RestockObserver
{
    public function created(InventoryHistory $history): void
    {
        if ($history->quantity <= 0) {
            return;
        }

        $stockable = $history->stockable;

        if (! $stockable instanceof Product && ! $stockable instanceof ProductVariant) {
            return;
        }

        $before = $stockable->getStock();
        $after = $before + $history->quantity;

        if ($before > 0 || $after <= 0) {
            return;
        }

        $product = $stockable instanceof ProductVariant ? $stockable->product : $stockable;

        if ($product instanceof Product) {
            NotifyStockSubscribers::dispatch($product->id)->afterCommit();
        }
    }
}
