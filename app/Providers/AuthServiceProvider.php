<?php

namespace App\Providers;

use App\Services\Auth\JwtGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Events\Dispatcher;
use App\Http\Middleware\VoyagerAdminMiddleware;
use App\Http\Controllers\ControllerCustomGates;

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

        //custom_genetvi_gate
        Gate::define('checkCategoryPermissionSisgeva', 'App\Http\Controllers\ControllerCustomGates@checkCategoryPermissionSisgeva');

    }
}
