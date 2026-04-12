<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Models\Shop;

class ProductPolicy
{

    public function create(User $user, Shop $shop): bool
    {
        return $user->id === $shop->user_id;
    }

    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->shop->user_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->shop->user_id || $user->isStaff();
    }
}