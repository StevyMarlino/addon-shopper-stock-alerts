<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Shopper\Models\Permission;
use Shopper\Models\Role;

final class StockAlertsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        if (! Permission::query()->where('name', 'alerts.browse')->exists()) {
            Permission::generate('alerts', 'inventory');
        }

        $administrator = Role::query()
            ->where('name', config('shopper.admin.roles.admin'))
            ->first();

        $administrator?->permissions()->syncWithoutDetaching(
            Permission::query()->where('name', 'like', 'alerts.%')->pluck('id')->all()
        );

        Schema::enableForeignKeyConstraints();
    }
}
