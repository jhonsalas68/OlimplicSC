<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            \App\Services\ActivityLogger::logGuest('login_fallido', "Usuario no encontrado: {$credentials['username']}");
            return back()->withErrors([
                'username' => 'No existe ese usuario.',
            ]);
        }

        if (!$user->is_active) {
            \App\Services\ActivityLogger::logGuest('login_fallido', "Intento de inicio de sesión de usuario inactivo: {$credentials['username']}");
            return back()->withErrors([
                'username' => 'Tu cuenta no está habilitada. Contacta al administrador.',
            ]);
        }

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            \App\Services\ActivityLogger::log('inicio_sesion', "El usuario inició sesión.");
            
            if ($user->hasRole('Coach')) {
                return redirect()->route('coach.dashboard');
            }

            return redirect()->intended('/admin/dashboard');
        }

        \App\Services\ActivityLogger::logGuest('login_fallido', "Contraseña incorrecta para el usuario: {$credentials['username']}");
        return back()->withErrors([
            'password' => 'Contraseña incorrecta.',
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            \App\Services\ActivityLogger::log('cierre_sesion', "El usuario cerró sesión.");
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
