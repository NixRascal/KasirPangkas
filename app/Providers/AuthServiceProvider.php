<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\CashLedger;
use App\Models\CashSession;
use App\Models\CommissionRule;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Shift;
use App\Policies\ActivityLogPolicy;
use App\Policies\CashLedgerPolicy;
use App\Policies\CashSessionPolicy;
use App\Policies\CommissionRulePolicy;
use App\Policies\CustomerPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\OrderItemPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ServiceCategoryPolicy;
use App\Policies\ServicePolicy;
use App\Policies\ShiftPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Service::class => ServicePolicy::class,
        ServiceCategory::class => ServiceCategoryPolicy::class,
        Employee::class => EmployeePolicy::class,
        Customer::class => CustomerPolicy::class,
        Shift::class => ShiftPolicy::class,
        CommissionRule::class => CommissionRulePolicy::class,
        Order::class => OrderPolicy::class,
        OrderItem::class => OrderItemPolicy::class,
        Payment::class => PaymentPolicy::class,
        CashSession::class => CashSessionPolicy::class,
        CashLedger::class => CashLedgerPolicy::class,
        ActivityLog::class => ActivityLogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-admin-panel', fn ($user) => $user->isAdmin());
        Gate::define('access-stakeholder-dashboard', fn ($user) => $user->isStakeholder() || $user->isAdmin());
    }
}
