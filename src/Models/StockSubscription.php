<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Shopper\Core\Models\Product;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $product_id
 * @property Carbon|null $notified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Product $product
 */
class StockSubscription extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return shopper_table('stock_subscriptions');
    }

    /**
     * @return BelongsTo<Model, $this>
     */
    public function customer(): BelongsTo
    {
        /** @var class-string<Model> $model */
        $model = config('auth.providers.users.model');

        return $this->belongsTo($model);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function markNotified(): void
    {
        $this->update(['notified_at' => now()]);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'notified_at' => 'datetime',
        ];
    }
}
