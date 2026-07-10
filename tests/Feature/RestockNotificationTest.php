<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Bus;
use Stevymarlino\AddonShopperStockAlerts\Jobs\NotifyStockSubscribers;

beforeEach(function (): void {
    $this->product = createProduct();
    $this->inventory = createInventory();
    Bus::fake();
});

it('dispatches the notify job when a product is restocked', function (): void {

    $this->product->setStock(newQuantity: 10, inventoryId: $this->inventory->id);

    Bus::assertDispatched(
        NotifyStockSubscribers::class,
        fn (NotifyStockSubscribers $job): bool => $job->productId === $this->product->id,
    );
});

it('dispatches the notify job when stock is added via mutateStock', function (): void {

    $this->product->mutateStock(inventoryId: $this->inventory->id, quantity: 10);

    Bus::assertDispatched(NotifyStockSubscribers::class);
});

it('does not dispatch when the product was already in stock', function (): void {

    $this->product->setStock(newQuantity: 5, inventoryId: $this->inventory->id);
    $this->product->setStock(newQuantity: 15, inventoryId: $this->inventory->id);

    Bus::assertDispatchedTimes(NotifyStockSubscribers::class, 1);
});

it('does not dispatch on a second positive mutation', function (): void {

    $this->product->mutateStock(inventoryId: $this->inventory->id, quantity: 10);
    $this->product->mutateStock(inventoryId: $this->inventory->id, quantity: 5);

    Bus::assertDispatchedTimes(NotifyStockSubscribers::class, 1);
});
