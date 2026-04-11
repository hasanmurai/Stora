<?php

namespace App\Providers;

use App\Models\{User, Shop};
use App\Policies\{UserPolicy, ShopPolicy};
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Shop::class, ShopPolicy::class);

        // Gate::before(function ($user, $ability) {
        //     return $user->isOwner() ? true : null;
        // });
    }
}