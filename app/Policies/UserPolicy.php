<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin || $user->is_staff;
    }

    public function view(User $user, User $model): bool
    {
        return $user->is_admin || $user->is_staff;
    }

    public function create(User $user): bool
    {
        return $user->is_admin || $user->is_staff;
    }

    public function update(User $user, User $model): bool
    {
        if ($user->is_admin) return true;
        // Staff can only edit non-staff, non-admin users
        return $user->is_staff && !$model->is_staff && !$model->is_admin;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->is_admin === true;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->is_admin === true;
    }
}
