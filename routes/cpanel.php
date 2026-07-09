<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/** @var array<string, class-string> $components */
$components = config('stock-alerts.components');

Route::get('stock-alerts/subscriptions', $components['subscription-index'])
    ->name('stock-alerts.subscriptions.index');
