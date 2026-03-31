<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Payment;
use App\Models\User;
use App\Models\Category;
use App\Models\Training;
use App\Exports\AthleteExport;
use App\Exports\PaymentExport;
use App\Exports\UserExport;
use App\Imports\AthleteImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class SuperAdminController extends Controller
{
    public function index()
    {
        $stats = [
            'atletas'      => Athlete::count(),
            'pagos'        => Payment::count(),
            'usuarios'     => User::count(),
            'categorias'   => Category::count(),
            'planificaciones' => Training::count(),
            'total_cobrado' => Payment::where('estado_pago', 'pagado')->sum('monto'),
        ];
        return view('admin.superadmin.index', compact('stats'));
    }

    // ── EXPORTS EXCEL ──────────────────────────────────────────────

    public function exportAtletasExcel()
    {
        return Excel::download(new AthleteExport, 'atletas_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPagosExcel()
    {
        return Excel::download(new PaymentExport, 'pagos_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportUsuariosExcel()
    {
        return Excel::download(new UserExport, 'usuarios_' . now()->format('Y-m-d') . '.xlsx');
    }

    // ── EXPORTS PDF ────────────────────────────────────────────────

    public function exportAtletasPdf()
    {
        $atletas = Athlete::with('category')->orderBy('apellido_paterno')->get();
        $pdf = Pdf::loadView('admin.superadmin.pdf.atletas', compact('atletas'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('atletas_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportPagosPdf()
    {
        $pagos = Payment::with('athlete', 'cobrador')->latest()->get();
        $pdf = Pdf::loadView('admin.superadmin.pdf.pagos', compact('pagos'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('pagos_' . now()->format('Y-m-d') . '.pdf');
    }

    // ── IMPORT EXCEL ───────────────────────────────────────────────

    public function importAtletas(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls|max:5120']);
        try {
            Excel::import(new AthleteImport, $request->file('file'));
            return back()->with('success', 'Atletas importados correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    // ── BACKUP ─────────────────────────────────────────────────────

    public function backup()
    {
        $tables = ['users', 'athletes', 'categories', 'payments', 'trainings', 'roles', 'permissions', 'model_has_roles', 'model_has_permissions', 'role_has_permissions'];
        $sql  = "-- ============================================================\n";
        $sql .= "-- Backup OlimpicSC | " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- ============================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            try {
                $sql .= "-- ------------------------------------------------------------\n";
                $sql .= "-- Tabla: `{$table}`\n";
                $sql .= "-- ------------------------------------------------------------\n";

                // DROP + CREATE TABLE
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

                // Obtener el CREATE TABLE desde SQLite o MySQL
                try {
                    $createResult = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$table]);
                    if (!empty($createResult)) {
                        $createSql = $createResult[0]->sql;
                        $sql .= $createSql . ";\n\n";
                    }
                } catch (\Exception $e) {
                    // MySQL fallback
                    try {
                        $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
                        if (!empty($createResult)) {
                            $row = (array) $createResult[0];
                            $createSql = end($row);
                            $sql .= $createSql . ";\n\n";
                        }
                    } catch (\Exception $e2) {
                        $sql .= "-- No se pudo obtener estructura: " . $e2->getMessage() . "\n\n";
                    }
                }

                // INSERT datos
                $rows = DB::table($table)->get();
                if ($rows->isEmpty()) {
                    $sql .= "-- (sin datos)\n\n";
                    continue;
                }

                foreach ($rows as $row) {
                    $row  = (array) $row;
                    $cols = implode(', ', array_map(fn($c) => "`$c`", array_keys($row)));
                    $vals = implode(', ', array_map(function ($v) {
                        if (is_null($v))   return 'NULL';
                        if (is_numeric($v)) return $v;
                        return "'" . addslashes((string) $v) . "'";
                    }, array_values($row)));
                    $sql .= "INSERT INTO `{$table}` ({$cols}) VALUES ({$vals});\n";
                }
                $sql .= "\n";

            } catch (\Exception $e) {
                $sql .= "-- Error en tabla {$table}: " . $e->getMessage() . "\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'backup_olimpicsc_' . now()->format('Y-m-d_H-i-s') . '.sql';

        return response($sql, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function backupExcel()
    {
        // Multi-sheet Excel con todas las tablas
        $filename = 'backup_excel_olimpicsc_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new \App\Exports\FullBackupExport, $filename);
    }

    // ── RESTORAGE (RESTORE) ────────────────────────────────────────

    public function restore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:text/plain,application/sql,application/octet-stream|max:10240', // 10MB .sql extension validation can be tricky, so we use max
        ]);

        try {
            $sql = file_get_contents($request->file('file')->getRealPath());
            
            // Validate if it is somewhat an SQL string
            if (empty(trim($sql)) || !str_contains($sql, 'INSERT INTO')) {
                 return back()->with('error', 'El archivo no parece ser un backup SQL válido.');
            }

            // Ejecuta el backup masivo
            DB::unprepared($sql);

            return back()->with('success', '¡Base de datos restaurada y reemplazada correctamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error crítico al restaurar la base de datos: ' . $e->getMessage());
        }
    }
}
