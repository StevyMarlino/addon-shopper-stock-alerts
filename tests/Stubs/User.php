<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Tests\Stubs;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $guarded = [];
}
