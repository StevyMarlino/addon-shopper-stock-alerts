# Shopper Stock Alerts

A back-in-stock alerts add-on for [Laravel Shopper](https://github.com/shopperlabs/shopper).

Customers subscribe to out-of-stock products and are **automatically notified** (mail + database) as soon as the product is restocked. The add-on ships with a customer API, an automatic restock detector, and an admin view of pending subscriptions.

- **Package:** `stevymarlino/addon-shopper-stock-alerts`
- **Namespace:** `Stevymarlino\AddonShopperStockAlerts`

## Table of contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Overriding the exposed component](#overriding-the-exposed-component)
- [Testing and code quality](#testing-and-code-quality)
- [How it works](#how-it-works)
- [License](#license)

## Requirements

- PHP **8.3+**
- Laravel **13.x**
- Shopper **3.x**

## Installation

Shopper 3.x is currently distributed from source (there is no stable `3.0` release on Packagist yet). You therefore install Shopper **from its GitHub source**, then require this add-on on top of a standard Shopper installation.

### 1. Install Shopper 3.x from source

In the `composer.json` of your Laravel 13 project, declare the Shopper source repositories and the development stability:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "/chemin/vers/shopper/packages/*"
    }
  ]
}
```

Then install Shopper and run its installer:

```bash
composer require shopper/framework:"*"
php artisan shopper:install
php artisan shopper:user
```

> **Note.** Complete the installation (migrations, asset publishing, etc.) by following the official [documentation](https://docs.laravelshopper.dev/v2/user-guide/getting-started/introduction)



### 2. Install the add-on

```bash
composer require stevymarlino/addon-shopper-stock-alerts
```

### 3. Run the migrations

```bash
php artisan migrate
```

This creates the `sh_stock_subscriptions` table. The **database** notification channel also relies on Laravel's `notifications` table; if your app does not have it yet:

```bash
php artisan make:notifications-table
php artisan migrate
```

### 4. Run a queue worker

Notifications are queued (`ShouldQueue`). Make sure a worker is running so alerts are delivered:

```bash
php artisan queue:work
```

## Configuration

### Enabling / disabling the add-on

The add-on registers itself through Shopper's add-on system. Toggle it in `config/shopper/addons.php`:

```php
return [
    // ...
    'stock-alerts' => true, // set to false to disable routes, observer and admin page
];
```

When disabled, the restock observer, the customer API and the admin page are all turned off.

### Publishing the add-on config

The add-on ships a small config file that declares its overridable component. Publish it if you need to customize it:

```bash
php artisan vendor:publish --tag=shopper-config
```

This creates `config/stock-alerts.php`.

## Usage

### Subscribing (customer API)

Three authenticated endpoints are exposed (protected by the `web` + `auth` middleware — the customer is resolved from the authenticated session, never from the request body):

| Method   | URI                                                | Description                                   |
|----------|----------------------------------------------------|-----------------------------------------------|
| `GET`    | `/stock-alerts/subscriptions`                      | List the authenticated customer's subscriptions |
| `POST`   | `/stock-alerts/products/{product}/subscriptions`   | Subscribe to a product (`422` if in stock)    |
| `DELETE` | `/stock-alerts/products/{product}/subscriptions`   | Unsubscribe from a product                    |

Route names: `stock-alerts.subscriptions.index`, `stock-alerts.subscriptions.store`, `stock-alerts.subscriptions.destroy`.

A customer can only subscribe to a product that is **currently out of stock**; subscribing to an in-stock product returns `422`.

### Automatic notification on restock

When a product's total stock transitions from **0 to a positive quantity** (through any of Shopper's stock mutations — `setStock`, `mutateStock`), the add-on:

1. detects the restock,
2. sends a queued `BackInStockNotification` (mail **and** database) to every **pending** subscriber of that product,
3. marks each subscription as notified.

Subscriptions are **single-use**: once notified, a subscription leaves the pending queue. A customer who wants to be alerted again simply subscribes again. This matches the "pending subscriptions" model of the requirement and avoids duplicate alerts.

### Admin

A **Stock Alerts** group appears in the cpanel sidebar, with a **Subscriptions** page listing the pending subscriptions by default (with a filter to also show notified ones). Each row shows the product, the customer, the subscription date and its status.

## Overriding the exposed component

The admin page is resolved from configuration, so the final application can replace it with its own Livewire component.

1. Publish the config (see above).
2. In `config/stock-alerts.php`, point the component key to your own class:

```php
use Stevymarlino\AddonShopperStockAlerts\Livewire\Pages\SubscriptionStock\Index;

return [
    'components' => [
        // Replace the default admin page with your own component.
        'subscription-index' => Index::class,
    ],
];
```

Your class typically extends the shipped component:

```php
namespace App\Livewire;

use Stevymarlino\AddonShopperStockAlerts\Livewire\Pages\SubscriptionStock\Index;

class CustomSubscriptionIndex extends Index
{
    // customize the table, columns, actions...
}
```

The route and the registered Livewire component both resolve from this config key, so your class is used everywhere the default one was.

## Testing and code quality

The package ships with a [Pest](https://pestphp.com) test suite that boots Shopper through [Testbench](https://github.com/orchestral/testbench).

### Prerequisite

To run the suite in isolation, the add-on needs Shopper available in **its own** `vendor/`. Declare the Shopper source (path or VCS repositories) in the package `composer.json`, then install:

```bash
composer install
```

### Commands

```bash
# Tests
vendor/bin/pest

# Code style (aligned with Shopper's Pint preset)
vendor/bin/pint --test

# Static analysis (PHPStan level 6 + Larastan)
vendor/bin/phpstan analyse

# Automated refactoring check (dry-run)
vendor/bin/rector process --dry-run
```

The test suite covers every route, the restock-notification business action, and the component override.

## How it works

Shopper stores stock polymorphically: both `Product` and `ProductVariant` use the `HasStock` trait, and every stock movement is written to the `inventory_histories` ledger before the `stock_levels` snapshot is updated.

Shopper does **not** emit a "back in stock" event. This add-on therefore observes `InventoryHistory` creation: on each positive movement it reads the stockable's current stock, computes the stock before the movement, and — when the stock crosses from `0` to a positive value — resolves the owning product and notifies its pending subscribers. Observing the ledger (rather than the snapshot, which is mutated with raw `increment()` calls that bypass Eloquent events) is what makes the detection reliable.

## License

The MIT License (MIT). See the `LICENSE` file for details.