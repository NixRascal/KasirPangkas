<?php

namespace App\Providers;

use App\Events\CashSessionClosed;
use App\Events\OrderPaid;
use App\Events\PriceOverridden;
use App\Listeners\GenerateCashSummary;
use App\Listeners\GenerateCommission;
use App\Listeners\LogPriceOverride;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPaid::class => [
            GenerateCommission::class,
        ],
        PriceOverridden::class => [
            LogPriceOverride::class,
        ],
        CashSessionClosed::class => [
            GenerateCashSummary::class,
        ],
    ];
}
