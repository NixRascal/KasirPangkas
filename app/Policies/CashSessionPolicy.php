<?php

namespace App\Policies;

use App\Models\CashSession;
use App\Models\User;

class CashSessionPolicy
{
    public function open(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function close(User $user, CashSession $session): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }
}
