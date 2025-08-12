<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        Gate::policy(Client::class, 'App\Policies\ClientPolicy');
        Gate::policy(User::class, 'App\Policies\UserPolicy');
    }
}
