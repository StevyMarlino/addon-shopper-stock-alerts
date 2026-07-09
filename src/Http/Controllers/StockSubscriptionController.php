<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Shopper\Core\Models\Product;
use Stevymarlino\AddonShopperStockAlerts\Models\StockSubscription;

final class StockSubscriptionController
{
    public function index(Request $request): JsonResponse
    {
        /** @var Authenticatable $customer */
        $customer = $request->user();

        $subscriptions = StockSubscription::query()
            ->where('customer_id', $customer->getAuthIdentifier())
            ->with('product')
            ->latest()
            ->get();

        return response()->json(['data' => $subscriptions]);
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        if ($product->getStock() > 0) {
            return response()->json(
                ['message' => __('This product is already in stock.')],
                422,
            );
        }

        /** @var Authenticatable $customer */
        $customer = $request->user();

        $subscription = StockSubscription::query()->updateOrCreate(
            [
                'customer_id' => $customer->getAuthIdentifier(),
                'product_id' => $product->getKey(),
            ],
            [
                'notified_at' => null,
            ],
        );

        return response()->json(['data' => $subscription], 201);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        /** @var Authenticatable $customer */
        $customer = $request->user();

        StockSubscription::query()
            ->where('customer_id', $customer->getAuthIdentifier())
            ->where('product_id', $product->getKey())
            ->delete();

        return response()->json(status: 204);
    }
}
