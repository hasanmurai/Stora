<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShopPolicy
{
    /**
     * Determine if the user can update the shop.
     */
    public function update(User $user, Shop $shop): Response
    {
        // Only the person who created the shop can edit its details
        return $user->id === $shop->user_id
            ? Response::allow()
            : Response::deny('You do not own this shop.');
    }

    /**
     * Determine if the user can delete the shop.
     */
    public function delete(User $user, Shop $shop): Response
    {
        // 1. The Shop Owner can delete their own shop
        if ($user->id === $shop->user_id) {
            return Response::allow();
        }

        // 2. An App Admin or Owner can delete ANY shop (Moderation)
        if ($user->isAdmin() || $user->isOwner()) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to delete this shop.');
    }
}