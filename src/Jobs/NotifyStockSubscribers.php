<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Shopper\Core\Models\Product;
use Stevymarlino\AddonShopperStockAlerts\Models\StockSubscription;
use Stevymarlino\AddonShopperStockAlerts\Notifications\BackInStockNotification;

final class NotifyStockSubscribers implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $productId
    ) {}

    public function handle(): void
    {
        $product = Product::query()->find($this->productId);

        if (! $product instanceof Product) {
            return;
        }

        StockSubscription::query()
            ->where('product_id', $this->productId)
            ->whereNull('notified_at')
            ->with('customer')
            ->chunkById(500, function ($subscriptions) use ($product): void {
                foreach ($subscriptions as $subscription) {
                    Notification::send($subscription->customer, new BackInStockNotification($product));
                }

                StockSubscription::query()
                    ->whereKey($subscriptions->modelKeys())
                    ->update(['notified_at' => now()]);
            });
    }
}
