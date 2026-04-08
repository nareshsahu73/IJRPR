<?php

namespace App\Providers;

use App\Models\Paper;
use App\Policies\PaperPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Paper::class => PaperPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(fn ($user) => $user->is_admin ? true : null);

        // Staff cannot delete users — enforced at route/gate level
        Gate::define('delete-user', fn ($user) => $user->is_admin === true);

        // Block staff from deleting via any route
        \Illuminate\Support\Facades\Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);
    }
}
