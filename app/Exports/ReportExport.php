<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $pagos;
    protected $rango;
    protected $metodo;

    public function __construct($pagos, $rango, $metodo)
    {
        $this->pagos = $pagos;
        $this->rango = mb_strtoupper($rango);
        $this->metodo = mb_strtoupper($metodo);
    }

    public function title(): string
    {
        return 'Transacciones';
    }

    public function collection()
    {
        return $this->pagos;
    }

    public function headings(): array
    {
        return [
            ['Reporte Financiero OlimpicSC'],
            ['Filtro de Tiempo: ' . $this->rango . ' | Método de Pago: ' . $this->metodo],
            ['', '', '', '', ''],
            [
                'ID Transacción',
                'Fecha',
                'Estudiante/Atleta',
                'C.I.',
                'Categoría',
                'Concepto',
                'Mes (Mensualidad)',
                'Descripción',
                'Método de Pago',
                'Total (Bs)'
            ]
        ];
    }

    public function map($pago): array
    {
        return [
            $pago->id,
            $pago->created_at->format('Y-m-d H:i:s'),
            $pago->athlete->nombre . ' ' . $pago->athlete->apellido_paterno . ' ' . $pago->athlete->apellido_materno,
            $pago->athlete->ci,
            $pago->athlete->category->nombre ?? 'Sin categoría',
            ucfirst($pago->concepto),
            $pago->mes_correspondiente ?? '-',
            $pago->descripcion ?? '-',
            ucfirst($pago->metodo_pago),
            $pago->monto,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 35,
            'D' => 15,
            'E' => 15,
            'F' => 20,
            'G' => 20,
            'H' => 30,
            'I' => 15,
            'J' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Título Principal
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Subtítulo
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '333333']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Fila 4: Encabezados Reales
        $sheet->getStyle('A4:J4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E3A8A']]]
        ]);

        // Datos (A5 hasta el final)
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 4) {
            $sheet->getStyle('A5:J'.$lastRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ]);
            
            // Format monto column
            $sheet->getStyle('J5:J'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        }

        return [];
    }
}
