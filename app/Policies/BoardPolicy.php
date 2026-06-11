<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;

class BoardPolicy
{
    public function view(User $user, Board $board): bool
    {
        return $board->user_id === $user->id
            || $board->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Board $board): bool
    {
        return $board->user_id === $user->id;
    }

    public function delete(User $user, Board $board): bool
    {
        return $board->user_id === $user->id;
    }
}
