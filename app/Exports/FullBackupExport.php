<?php

namespace App\Exports;

use App\Models\Athlete;
use App\Models\Payment;
use App\Models\User;
use App\Models\Category;
use App\Models\Training;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FullBackupExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new BackupSheet('Atletas',        Athlete::with('category')->get(),   $this->atletasHeadings(),   fn($r) => $this->atletasMap($r)),
            new BackupSheet('Pagos',          Payment::with('athlete')->get(),    $this->pagosHeadings(),     fn($r) => $this->pagosMap($r)),
            new BackupSheet('Usuarios',       User::with('roles','category')->get(), $this->usuariosHeadings(), fn($r) => $this->usuariosMap($r)),
            new BackupSheet('Categorias',     Category::all(),                    $this->categoriasHeadings(), fn($r) => $this->categoriasMap($r)),
            new BackupSheet('Planificaciones',Training::with('category')->get(),  $this->planificacionesHeadings(), fn($r) => $this->planificacionesMap($r)),
        ];
    }

    private function atletasHeadings(): array {
        return ['ID','Nombre','Ape. Paterno','Ape. Materno','CI','Categoría','Fecha Nac.','Género','Habilitado','Seguro','Alergias','Creado'];
    }
    private function atletasMap($a): array {
        return [$a->id, $a->nombre, $a->apellido_paterno, $a->apellido_materno, $a->ci, $a->category->nombre ?? '—', $a->fecha_nacimiento?->format('d/m/Y'), $a->genero, $a->habilitado_booleano ? 'SÍ' : 'NO', $a->tiene_seguro ? 'SÍ' : 'NO', $a->alergias, $a->created_at->format('d/m/Y')];
    }

    private function pagosHeadings(): array {
        return ['ID','Atleta','CI','Concepto','Descripción','Mes','Monto','Método','Estado','Fecha'];
    }
    private function pagosMap($p): array {
        return [$p->id, trim(($p->athlete->nombre ?? '') . ' ' . ($p->athlete->apellido_paterno ?? '')), $p->athlete->ci ?? '—', $p->concepto, $p->descripcion, $p->mes_correspondiente, $p->monto, $p->metodo_pago, $p->estado_pago, $p->created_at->format('d/m/Y H:i')];
    }

    private function usuariosHeadings(): array {
        return ['ID','Nombre','Ape. Paterno','Username','Email','Rol','Categoría','Creado'];
    }
    private function usuariosMap($u): array {
        return [$u->id, $u->name, $u->apellido_paterno, $u->username, $u->email, $u->roles->pluck('name')->implode(', '), $u->category->nombre ?? '—', $u->created_at->format('d/m/Y')];
    }

    private function categoriasHeadings(): array {
        return ['ID','Nombre','Edad Mín.','Edad Máx.','Descripción'];
    }
    private function categoriasMap($c): array {
        return [$c->id, $c->nombre, $c->edad_min, $c->edad_max, $c->descripcion ?? '—'];
    }

    private function planificacionesHeadings(): array {
        return ['ID','Título','Categoría','Descripción','Fecha','Creado'];
    }
    private function planificacionesMap($t): array {
        return [$t->id, $t->titulo ?? $t->title ?? '—', $t->category->nombre ?? '—', $t->descripcion ?? $t->description ?? '—', $t->fecha ?? $t->date ?? '—', $t->created_at->format('d/m/Y')];
    }
}

/**
 * Sheet genérico reutilizable para el backup
 */
class BackupSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping
{
    public function __construct(
        private string $title,
        private $collection,
        private array  $headings,
        private        $mapper
    ) {}

    public function title(): string { return $this->title; }

    public function collection() { return $this->collection; }

    public function headings(): array { return $this->headings; }

    public function map($row): array { return ($this->mapper)($row); }

    public function styles(Worksheet $sheet): array {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }
}
