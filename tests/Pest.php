<?php

declare(strict_types=1);

use Stevymarlino\AddonShopperStockAlerts\Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature');

function createProduct(): Shopper\Core\Models\Product
{
    return Shopper\Core\Models\Product::factory()->create();
}

function createInventory(): Shopper\Core\Models\Inventory
{
    return Shopper\Core\Models\Inventory::factory()->create();
}

function createUser(string $email = 'mia@homedepot.test'): Stevymarlino\AddonShopperStockAlerts\Tests\Stubs\User
{
    return Stevymarlino\AddonShopperStockAlerts\Tests\Stubs\User::query()->create([
        'first_name' => 'Mia',
        'last_name' => 'Mala',
        'email' => $email,
        'password' => 'secret',
    ]);
}
