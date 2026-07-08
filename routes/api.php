<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stevymarlino\AddonShopperStockAlerts\Http\Controllers\StockSubscriptionController;

Route::middleware(['web', 'auth'])
    ->prefix('stock-alerts')
    ->name('stock-alerts.')
    ->group(function (): void {
        Route::get('subscriptions', [StockSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('products/{product}/subscriptions', [StockSubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::delete('products/{product}/subscriptions', [StockSubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
    });
