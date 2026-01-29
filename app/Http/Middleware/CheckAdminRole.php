<?php

    namespace App\Http\Middleware;

    use Closure;

    class CheckAdminRole
    {
        /**
         * @param \Illuminate\Http\Request $request
         * @param \Closure $next
         * @return mixed|string
         */
        public function handle($request, Closure $next)
        {
            $user = auth()->user();

            if (($user->isAdmin() or $user->checkBackendAccess()) and 1 == $user->active ) {
                return $next($request);
            }

            auth()->logout();
            return redirect()->route('login');
        }
    }
