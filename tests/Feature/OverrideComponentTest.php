<?php

declare(strict_types=1);

use Stevymarlino\AddonShopperStockAlerts\Livewire\Pages\SubscriptionStock\Index;

it('uses the default subscription index component', function (): void {
    expect(config('stock-alerts.components.subscription-index'))
        ->toBe(Index::class);
});

it('resolves the overridden component from config', function (): void {
    config()->set('stock-alerts.components.subscription-index', CustomSubscriptionIndex::class);

    $component = config('stock-alerts.components.subscription-index');

    expect($component)->toBe(CustomSubscriptionIndex::class)
        ->and(class_exists($component))->toBeTrue();
});

class CustomSubscriptionIndex extends Index {}
