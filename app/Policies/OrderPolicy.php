<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier() || $user->isStakeholder();
    }

    public function view(User $user, Order $order): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}
