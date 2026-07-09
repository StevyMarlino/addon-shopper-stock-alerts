<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('boots shopper and runs the addon migration', function (): void {
    expect(Schema::hasTable(shopper_table('products')))->toBeTrue()
        ->and(Schema::hasTable(shopper_table('inventory_histories')))->toBeTrue()
        ->and(Schema::hasTable(shopper_table('stock_subscriptions')))->toBeTrue();
});