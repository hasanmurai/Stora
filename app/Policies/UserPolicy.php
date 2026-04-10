<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function toggleStatus(User $user, User $target): Response
    {
        if ($target->isOwner()) {
            return Response::deny('The Owner account cannot be deactivated.');
        }

        if ($user->id === $target->id) {
            return Response::deny('You cannot ban your own account.');
        }

        if ($user->isAdmin()) {
            return $target->role === 'user' 
                ? Response::allow() 
                : Response::deny('Admins do not have permission to ban other staff members.');
        }

        if ($user->isOwner()) {
            return Response::allow();
        }

        return Response::deny('You do not have administrative privileges.');
    }

    public function changeRole(User $user, User $target): bool
    {
        if (!$user->isOwner()) {
            return false;
        }

        if ($user->id === $target->id) {
            return false;
        }

        return true;
    }

}
