<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Shopper\Core\Models\Product;

final class BackInStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Product $product
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Back in stock: :product', ['product' => $this->product->name]))
            ->line(__('Good news! ":product" is available again.', ['product' => $this->product->name]))
            ->action(__('View product'), url('/'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->getKey(),
            'product_name' => $this->product->name,
        ];
    }
}
