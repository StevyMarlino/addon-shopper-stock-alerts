<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Shopper\Core\Helpers\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create($this->getTableName('stock_subscriptions'), function (Blueprint $table): void {
            $this->addCommonFields($table);

            $this->addForeignKey($table, 'customer_id', 'users', nullable: false);
            $this->addForeignKey($table, 'product_id', $this->getTableName('products'), nullable: false);

            $table->timestamp('notified_at')->nullable()->index();

            $table->unique(['customer_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->getTableName('stock_subscriptions'));
    }
};
