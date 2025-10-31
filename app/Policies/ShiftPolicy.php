<?php

namespace App\Policies;

use App\Models\Shift;
use App\Models\User;

class ShiftPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier() || $user->isStakeholder();
    }

    public function view(User $user, Shift $shift): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Shift $shift): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Shift $shift): bool
    {
        return $user->isAdmin();
    }
}
