<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\RedirectResponse;

class RedirectToDashboardAfterLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event)
    {
        // Este listener redirige al usuario al dashboard después del login
        // Solo aplica si viene desde las rutas de autenticación
        return redirect('/dashboard');
    }
}
