<?php

namespace App\Policies;

use App\Models\OrderItem;
use App\Models\User;

class OrderItemPolicy
{
    public function update(User $user, OrderItem $item): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function delete(User $user, OrderItem $item): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function overridePrice(User $user, OrderItem $item): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }
}
