<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UserExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string { return 'Usuarios'; }

    public function collection()
    {
        return User::with('roles', 'category')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nombres', 'Ape. Paterno', 'Ape. Materno', 'Username', 'Email', 'CI', 'Rol', 'Categoría', 'Activo', 'Creado'];
    }

    public function map($u): array
    {
        return [
            $u->id,
            $u->name,
            $u->apellido_paterno,
            $u->apellido_materno,
            $u->username,
            $u->email,
            $u->ci,
            $u->roles->pluck('name')->implode(', '),
            $u->category->nombre ?? '—',
            $u->is_active ? 'SÍ' : 'NO',
            $u->created_at->format('d/m/Y'),
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 20, 'C' => 18, 'D' => 18, 'E' => 12, 'F' => 28, 'G' => 14, 'H' => 12, 'I' => 14, 'J' => 8, 'K' => 12];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
