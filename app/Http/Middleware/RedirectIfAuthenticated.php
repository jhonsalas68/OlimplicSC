<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario intenta acceder al panel de Filament y ya está autenticado,
        // lo redirigimos al dashboard personal
        if (Auth::check() && str_starts_with($request->path(), 'admin-panel')) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}


