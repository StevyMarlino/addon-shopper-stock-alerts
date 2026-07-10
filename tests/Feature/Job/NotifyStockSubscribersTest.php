<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Notification;
use Stevymarlino\AddonShopperStockAlerts\Jobs\NotifyStockSubscribers;
use Stevymarlino\AddonShopperStockAlerts\Models\StockSubscription;
use Stevymarlino\AddonShopperStockAlerts\Notifications\BackInStockNotification;

beforeEach(function (): void {
    $this->product = createProduct();
    $this->user = createUser();

    Notification::fake();
});

it('notifies pending subscribers and marks them notified', function (): void {
    $product = $this->product;
    $customer = $this->user;

    $subscription = StockSubscription::query()->create([
        'customer_id' => $customer->id,
        'product_id' => $product->id,
    ]);

    (new NotifyStockSubscribers($product->id))->handle();

    Notification::assertSentTo($customer, BackInStockNotification::class);

    expect($subscription->refresh()->notified_at)->not->toBeNull();
});

it('skips subscriptions already notified', function (): void {
    $product = $this->product;
    $customer = $this->user;

    StockSubscription::query()->create([
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'notified_at' => now(),
    ]);

    (new NotifyStockSubscribers($product->id))->handle();

    Notification::assertNotSentTo($customer, BackInStockNotification::class);
});
