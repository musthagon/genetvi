<?php

namespace App\Http\Middleware;

use Closure;

class VoyagerAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!app('VoyagerAuth')->guest()) {
            $user = app('VoyagerAuth')->user();
            app()->setLocale($user->locale ?? app()->getLocale());

            return $user->hasPermission('browse_admin') ? $next($request) : redirect('/');
        }

        //$urlLogin = route('voyager.login');

        $urlLogin = route('login');

        return redirect()->guest($urlLogin);
    }
}
