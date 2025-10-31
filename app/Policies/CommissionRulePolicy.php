<?php

namespace App\Policies;

use App\Models\CommissionRule;
use App\Models\User;

class CommissionRulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier() || $user->isStakeholder();
    }

    public function view(User $user, CommissionRule $rule): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, CommissionRule $rule): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, CommissionRule $rule): bool
    {
        return $user->isAdmin();
    }
}
