<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Notification;
use Stevymarlino\AddonShopperStockAlerts\Models\StockSubscription;
use Stevymarlino\AddonShopperStockAlerts\Notifications\BackInStockNotification;

beforeEach(function (): void {
    $this->user = createUser();
    $this->product = createProduct();
    $this->inventory = createInventory();
});

it('notifies pending subscribers when a product is restocked', function (): void {
    Notification::fake();

    $inventory = $this->inventory;
    $product = $this->product;
    $customer = $this->user;

    $subscription = StockSubscription::query()->create([
        'customer_id' => $customer->id,
        'product_id' => $product->id,
    ]);

    $product->setStock(newQuantity: 10, inventoryId: $inventory->id);

    Notification::assertSentTo($customer, BackInStockNotification::class);

    expect($subscription->refresh()->notified_at)->not->toBeNull();
});

it('does not notify when the product was already in stock', function (): void {
    Notification::fake();

    $inventory = $this->inventory;
    $product = $this->product;
    $product->setStock(newQuantity: 5, inventoryId: $inventory->id);

    $customer = $this->user;

    StockSubscription::query()->create([
        'customer_id' => $customer->id,
        'product_id' => $product->id,
    ]);

    $product->setStock(newQuantity: 15, inventoryId: $inventory->id);

    Notification::assertNotSentTo($customer, BackInStockNotification::class);
});

it('notifies subscribers when stock is added via mutateStock', function (): void {
    Notification::fake();

    $inventory = $this->inventory;
    $product = $this->product;

    $customer = $this->user;

    $subscription = StockSubscription::query()->create([
        'customer_id' => $customer->id,
        'product_id' => $product->id,
    ]);

    $product->mutateStock(inventoryId: $inventory->id, quantity: 10);

    Notification::assertSentTo($customer, BackInStockNotification::class);

    expect($subscription->refresh()->notified_at)->not->toBeNull();
});

it('does not notify when the product was already in stock with mutation', function (): void {
    Notification::fake();

    $inventory = $this->inventory;
    $product = $this->product;
    $customer = $this->user;

    StockSubscription::query()->create([
        'customer_id' => $customer->id, 'product_id' => $product->id,
    ]);

    $product->mutateStock(inventoryId: $inventory->id, quantity: 10);
    $product->mutateStock(inventoryId: $inventory->id, quantity: 5);

    Notification::assertSentToTimes($customer, BackInStockNotification::class, 1);
});

it('re-arms a notified subscription when the customer subscribes again', function (): void {
    $product = $this->product;
    $customer = $this->user;

    $subscription = StockSubscription::query()->create([
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'notified_at' => now(),
    ]);

    $this->actingAs($customer)
        ->postJson(route('stock-alerts.subscriptions.store', $product))
        ->assertCreated();

    expect($subscription->refresh()->notified_at)->toBeNull()
        ->and(StockSubscription::query()->count())->toBe(1);
});
