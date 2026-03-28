<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
})->name('inicio');

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Compatibility Redirect
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Protected Routes
Route::middleware(['auth', 'prevent-back-history'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Module Atletas
    Route::get('/athletes/export', [\App\Http\Controllers\Admin\AthleteController::class, 'export'])->name('athletes.export');
    Route::post('/athletes/export/selected', [\App\Http\Controllers\Admin\AthleteController::class, 'exportSelected'])->name('athletes.export.selected');
    Route::post('/athletes/import', [\App\Http\Controllers\Admin\AthleteController::class, 'import'])->name('athletes.import');
    Route::post('/athletes/{athlete}/toggle-habilitado', [\App\Http\Controllers\Admin\AthleteController::class, 'toggleHabilitado'])->name('athletes.toggle-habilitado');
    Route::resource('athletes', \App\Http\Controllers\Admin\AthleteController::class);

    // Module Usuarios
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // Module Pagos
    Route::get('/payments/export/pdf',   [\App\Http\Controllers\Admin\PaymentController::class, 'exportPdf'])->name('payments.export.pdf');
    Route::get('/payments/export/excel', [\App\Http\Controllers\Admin\PaymentController::class, 'exportExcel'])->name('payments.export.excel');
    Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class)->only(['index','destroy']);

    // Module Cobranza (legacy redirect → cobros)
    Route::get('/cobranza', fn() => redirect()->route('cobros.index'))->name('cobranza.index');
    Route::get('/cobranza/search', fn() => redirect()->route('cobros.index'))->name('cobranza.search');
    Route::post('/cobranza/buscar', fn() => redirect()->route('cobros.index'))->name('cobranza.buscar');
    Route::post('/cobranza/cobrar', fn() => redirect()->route('cobros.index'))->name('cobranza.cobrar');
    Route::get('/cobranza/nota/{payment}', fn($payment) => redirect()->route('cobros.nota', $payment))->name('cobranza.nota');

    // Module Planificaciones (Trainings)
    Route::resource('trainings', \App\Http\Controllers\Admin\TrainingController::class);

    // Module Reportes
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');

    // Module Cobros (panel de venta independiente)
    Route::get('/cobros', [\App\Http\Controllers\Admin\CobrosController::class, 'index'])->name('cobros.index');
    Route::get('/cobros/search', [\App\Http\Controllers\Admin\CobrosController::class, 'search'])->name('cobros.search');
    Route::get('/cobros/atleta/{athlete}', [\App\Http\Controllers\Admin\CobrosController::class, 'getAtleta'])->name('cobros.atleta');
    Route::post('/cobros/cobrar', [\App\Http\Controllers\Admin\CobrosController::class, 'cobrar'])->name('cobros.cobrar');
    Route::get('/cobros/nota/{payment}', [\App\Http\Controllers\Admin\CobrosController::class, 'nota'])->name('cobros.nota');

    // Module Coach
    Route::get('/coach/dashboard', [\App\Http\Controllers\Admin\CoachController::class, 'dashboard'])->name('coach.dashboard');
    Route::get('/coach/atletas', [\App\Http\Controllers\Admin\CoachController::class, 'atletas'])->name('coach.atletas');
    Route::get('/coach/planificaciones', [\App\Http\Controllers\Admin\CoachController::class, 'planificaciones'])->name('coach.planificaciones');

    // Module SuperAdmin
    Route::middleware(['role:SuperAdmin'])->prefix('superadmin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('superadmin.index');
        Route::get('/export/atletas/pdf',    [\App\Http\Controllers\Admin\SuperAdminController::class, 'exportAtletasPdf'])->name('superadmin.export.atletas.pdf');
        Route::get('/export/pagos/pdf',      [\App\Http\Controllers\Admin\SuperAdminController::class, 'exportPagosPdf'])->name('superadmin.export.pagos.pdf');
        Route::get('/export/atletas/excel',  [\App\Http\Controllers\Admin\SuperAdminController::class, 'exportAtletasExcel'])->name('superadmin.export.atletas.excel');
        Route::get('/export/pagos/excel',    [\App\Http\Controllers\Admin\SuperAdminController::class, 'exportPagosExcel'])->name('superadmin.export.pagos.excel');
        Route::get('/export/usuarios/excel', [\App\Http\Controllers\Admin\SuperAdminController::class, 'exportUsuariosExcel'])->name('superadmin.export.usuarios.excel');
        Route::post('/import/atletas',       [\App\Http\Controllers\Admin\SuperAdminController::class, 'importAtletas'])->name('superadmin.import.atletas');
        Route::get('/backup/sql',            [\App\Http\Controllers\Admin\SuperAdminController::class, 'backup'])->name('superadmin.backup.sql');
        Route::get('/backup/excel',          [\App\Http\Controllers\Admin\SuperAdminController::class, 'backupExcel'])->name('superadmin.backup.excel');
    });
});
