<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier() || $user->isStakeholder();
    }

    public function view(User $user, Customer $customer): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->isAdmin() || $user->isCashier();
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->isAdmin();
    }
}
