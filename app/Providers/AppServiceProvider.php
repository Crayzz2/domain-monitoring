<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Domain;
use App\Models\Hosting;
use App\Models\HostingProviders;
use App\Models\Status;
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
        Gate::policy(Domain::class, 'App\Policies\DomainPolicy');
        Gate::policy(Hosting::class, 'App\Policies\HostingPolicy');
        Gate::policy(HostingProviders::class, 'App\Policies\HostingProviderPolicy');
        Gate::policy(User::class, 'App\Policies\UserPolicy');
    }
}
