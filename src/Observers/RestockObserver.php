<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Observers;

use Illuminate\Support\Facades\Notification;
use Shopper\Core\Models\InventoryHistory;
use Shopper\Core\Models\Product;
use Shopper\Core\Models\ProductVariant;
use Stevymarlino\AddonShopperStockAlerts\Models\StockSubscription;
use Stevymarlino\AddonShopperStockAlerts\Notifications\BackInStockNotification;

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

        if (! $product instanceof Product) {
            return;
        }

        $subscriptions = StockSubscription::query()
            ->where('product_id', $product->getKey())
            ->whereNull('notified_at')
            ->with('customer')
            ->get();

        $subscriptions->each(function (StockSubscription $subscription) use ($product): void {
            Notification::send($subscription->customer, new BackInStockNotification($product));
            $subscription->markNotified();
        });
    }
}
