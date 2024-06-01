<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // Định nghĩa Gate cho các quyền
        Gate::define('view-users', function ($user) {
            return $user->hasPermissionTo('View', 'Users');
        });

        Gate::define('add-users', function ($user) {
            return $user->hasPermissionTo('Add', 'Users');
        });

        Gate::define('edit-users', function ($user) {
            return $user->hasPermissionTo('Edit', 'Users');
        });

        Gate::define('delete-users', function ($user) {
            return $user->hasPermissionTo('Delete', 'Users');
        });

        Gate::define('view-roles', function ($user) {
            return $user->hasPermissionTo('View', 'Role');
        });

        Gate::define('add-roles', function ($user) {
            return $user->hasPermissionTo('Add', 'Role');
        });

        Gate::define('delete-roles', function ($user) {
            return $user->hasPermissionTo('Delete', 'Role');
        });

        Gate::define('view-history', function ($user) {
            return $user->hasPermissionTo('View', 'History');
        });

        Gate::define('edit-roles', function ($user) {
            return $user->hasPermissionTo('Edit', 'Role');
        });
    }
}
