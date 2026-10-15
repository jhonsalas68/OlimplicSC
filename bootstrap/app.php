<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Confiar en todos los proxies (necesario para Railway / Heroku y HTTPS)
        $middleware->trustProxies(at: '*');

        // Aplicar middleware personalizado para redirigir después de login
        $middleware->web(append: [
            \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);

        // Registrar aliases de Spatie Permission
        $middleware->alias([
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Redirigir 403 (rol insuficiente) al dashboard
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }
            return redirect()->route('admin.dashboard')->with('error', 'No tienes permiso para acceder a esa sección.');
        });

        // Mostrar detalle exacto del error en lugar de la pantalla genérica 500
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return null;
            }
            \Illuminate\Support\Facades\Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response(
                "<div style='background:#1e1e2f;color:#fff;padding:30px;font-family:monospace;border-left:6px solid #e11d48;margin:20px;border-radius:10px;line-height:1.5;'>"
                . "<h2 style='color:#f43f5e;margin-top:0;'>DETALLE DEL ERROR 500: " . htmlspecialchars($e->getMessage()) . "</h2>"
                . "<p><b>Excepción:</b> " . get_class($e) . "</p>"
                . "<p><b>Archivo:</b> " . $e->getFile() . " (Línea: " . $e->getLine() . ")</p>"
                . "<details open style='margin-top:15px;'><summary style='cursor:pointer;color:#38bdf8;font-weight:bold;margin-bottom:10px;'>Pila de Ejecución (Stack Trace)</summary>"
                . "<pre style='background:#0f172a;color:#cbd5e1;padding:15px;border-radius:6px;overflow-x:auto;white-space:pre-wrap;font-size:12px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre></details>"
                . "</div>",
                500
            );
        });
    })->create();
