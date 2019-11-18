<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Events\Dispatcher;
use App\Http\Middleware\VoyagerAdminMiddleware;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',   
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(Router $router, Dispatcher $event)
    {
        $this->registerPolicies();

        //Custom Sisgeva Gates
        Gate::define('checkCategoryPermissionSisgeva', 'App\Http\Controllers\ControllerCustomGates@checkCategoryPermissionSisgeva');

        $router->aliasMiddleware('admin.user', VoyagerAdminMiddleware::class);
    }
}
