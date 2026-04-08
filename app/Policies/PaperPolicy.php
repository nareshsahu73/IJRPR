<?php

namespace App\Policies;

use App\Models\Paper;
use App\Models\User;

class PaperPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Paper $paper): bool
    {
        return $user->id === $paper->user_id || $user->is_admin || $user->is_staff;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Paper $paper): bool
    {
        return $user->id === $paper->user_id || $user->is_admin || $user->is_staff;
    }

    public function delete(User $user, Paper $paper): bool
    {
        return $user->is_admin === true;
    }
}
