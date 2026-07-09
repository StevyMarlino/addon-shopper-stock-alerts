<?php

declare(strict_types=1);

use Stevymarlino\AddonShopperStockAlerts\Models\StockSubscription;

beforeEach(function (): void {
    $this->user = createUser();
    $this->product = createProduct();
    $this->inventory = createInventory();
});

it('lets an authenticated customer subscribe to an out-of-stock product', function (): void {
    $product = $this->product;
    $customer = $this->user;

    $this->actingAs($customer)
        ->postJson(route('stock-alerts.subscriptions.store', $product))
        ->assertCreated();

    expect(StockSubscription::query()
        ->where('customer_id', $customer->id)
        ->where('product_id', $product->id)
        ->exists())->toBeTrue();
});

it('rejects subscribing to a product already in stock', function (): void {
    $inventory = $this->inventory;
    $product = $this->product;
    $product->setStock(newQuantity: 5, inventoryId: $inventory->id);

    $this->actingAs($this->user)
        ->postJson(route('stock-alerts.subscriptions.store', $product))
        ->assertStatus(422);
});

it('lets a customer unsubscribe from a product', function (): void {
    $product = $this->product;
    $customer = $this->user;
    StockSubscription::query()->create([
        'customer_id' => $customer->id,
        'product_id' => $product->id,
    ]);

    $this->actingAs($customer)
        ->deleteJson(route('stock-alerts.subscriptions.destroy', $product))
        ->assertNoContent();

    expect(StockSubscription::query()
        ->where('customer_id', $customer->id)
        ->where('product_id', $product->id)
        ->exists())->toBeFalse();
});

it('lists the subscriptions of the authenticated customer', function (): void {
    $customer = $this->user;
    $others = createUser('other@example.test');

    $mine = createProduct();
    $notMine = createProduct();

    StockSubscription::query()->create(['customer_id' => $customer->id, 'product_id' => $mine->id]);
    StockSubscription::query()->create(['customer_id' => $others->id, 'product_id' => $notMine->id]);

    $this->actingAs($customer)
        ->getJson(route('stock-alerts.subscriptions.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('refuses subscription for a guest', function (): void {
    $product = $this->product;

    $this->postJson(route('stock-alerts.subscriptions.store', $product))
        ->assertUnauthorized();
});
