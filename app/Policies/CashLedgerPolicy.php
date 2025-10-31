<?php

namespace App\Policies;

use App\Models\CashLedger;
use App\Models\User;

class CashLedgerPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }
}
