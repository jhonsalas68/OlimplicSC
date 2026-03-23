<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PaymentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function __construct(private $data = null) {}

    public function title(): string { return 'Pagos'; }

    public function collection()
    {
        return $this->data ?? Payment::with('athlete', 'cobrador')->latest()->get();
    }

    public function headings(): array
    {
        return ['ID', 'Atleta', 'CI Atleta', 'Concepto', 'Descripción', 'Mes', 'Monto (Bs.)', 'Método Pago', 'Estado', 'Cobrado Por', 'Fecha'];
    }

    public function map($p): array
    {
        return [
            $p->id,
            trim($p->athlete->nombre . ' ' . $p->athlete->apellido_paterno),
            $p->athlete->ci,
            $p->concepto,
            $p->descripcion,
            $p->mes_correspondiente,
            $p->monto,
            $p->metodo_pago,
            $p->estado_pago,
            trim(($p->cobrador->name ?? '') . ' ' . ($p->cobrador->apellido_paterno ?? '')),
            $p->created_at->format('d/m/Y H:i'),
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 24, 'C' => 14, 'D' => 18, 'E' => 28, 'F' => 12, 'G' => 12, 'H' => 12, 'I' => 10, 'J' => 22, 'K' => 18];
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
