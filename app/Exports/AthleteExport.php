<?php

namespace App\Exports;

use App\Models\Athlete;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AthleteExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    public function title(): string
    {
        return 'Atletas OlimpicSC';
    }

    public function collection()
    {
        return Athlete::with('category')->get();
    }

    public function headings(): array
    {
        return [
            'ID Alfanumérico',      // A - Auto-generado, no modificar
            'Nombres',              // B - Obligatorio
            'Apellido Paterno',     // C - Obligatorio
            'Apellido Materno',     // D - Opcional
            'C.I.',                 // E - Obligatorio, único
            'Categoría',            // F - Ej: Sub-10, Sub-12, Sub-15
            'Fecha de Nacimiento',  // G - Formato: DD/MM/AAAA
            'Género',               // H - Masculino / Femenino
            'Alergias',             // I - Opcional
            'Seguro Médico',        // J - Opcional
            'Tutor Nombres',        // K - Opcional
            'Tutor Ape. Paterno',   // L - Opcional
            'Tutor Ape. Materno',   // M - Opcional
            'Tutor Teléfono',       // N - Opcional
            'Habilitado',           // O - SÍ / NO
        ];
    }

    public function map($athlete): array
    {
        return [
            $athlete->id_alfanumerico_unico,
            $athlete->nombre,
            $athlete->apellido_paterno,
            $athlete->apellido_materno,
            $athlete->ci,
            $athlete->category->nombre ?? 'N/A',
            $athlete->fecha_nacimiento?->format('d/m/Y'),
            $athlete->genero,
            $athlete->alergias,
            $athlete->seguro_medico,
            $athlete->nombre_padre,
            $athlete->apellido_paterno_padre,
            $athlete->apellido_materno_padre,
            $athlete->telefono_padre,
            $athlete->habilitado_booleano ? 'SÍ' : 'NO',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,  // ID Alfanumérico
            'B' => 20,  // Nombres
            'C' => 20,  // Apellido Paterno
            'D' => 20,  // Apellido Materno
            'E' => 15,  // C.I.
            'F' => 14,  // Categoría
            'G' => 18,  // Fecha Nacimiento
            'H' => 13,  // Género
            'I' => 22,  // Alergias
            'J' => 22,  // Seguro Médico
            'K' => 20,  // Tutor Nombres
            'L' => 20,  // Tutor Ape. Paterno
            'M' => 20,  // Tutor Ape. Materno
            'N' => 18,  // Tutor Teléfono
            'O' => 12,  // Habilitado
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo de encabezados (fila 1)
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E3A5F'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'O';

                // Altura de la fila de encabezado
                $sheet->getRowDimension(1)->setRowHeight(40);

                // Congelar la fila de encabezados
                $sheet->freezePane('A2');

                // Alternar colores en las filas de datos para mejor lectura
                for ($row = 2; $row <= $lastRow; $row++) {
                    $color = ($row % 2 === 0) ? 'EBF3FB' : 'FFFFFF';
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $color],
                        ],
                        'font' => ['size' => 10],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'D0E4F7'],
                            ],
                        ],
                    ]);
                }

                // Centrar columnas específicas
                $centerCols = ['A', 'E', 'F', 'G', 'H', 'N', 'O'];
                foreach ($centerCols as $col) {
                    $sheet->getStyle("{$col}2:{$col}{$lastRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Agregar fila de ejemplo al final si no hay datos
                if ($lastRow === 1) {
                    $sheet->getStyle("A2:O2")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFF9C4'],
                        ],
                        'font' => ['italic' => true, 'color' => ['rgb' => '666666']],
                    ]);
                    $exampleRow = [
                        '(auto)', 'Juan', 'Pérez', 'López',
                        '12345678', 'Sub-12', '15/06/2012',
                        'Masculino', 'Ninguna', 'Ninguno',
                        'María', 'López', 'García', '77712345', 'SÍ',
                    ];
                    $sheet->fromArray($exampleRow, null, 'A2');
                    $sheet->getCell('A1')->setValue($sheet->getCell('A1')->getValue());
                }

                // Comentario de instrucciones en A1
                $sheet->getComment('A1')->getText()->createTextRun(
                    'INSTRUCCIONES DE IMPORTACIÓN:' . "\n" .
                    '- No modificar los encabezados.' . "\n" .
                    '- El campo ID es auto-generado.' . "\n" .
                    '- Fecha formato: DD/MM/AAAA.' . "\n" .
                    '- Género: "Masculino" o "Femenino".' . "\n" .
                    '- Habilitado: "SÍ" o "NO".' . "\n" .
                    '- La Categoría debe coincidir exactamente.'
                );
            },
        ];
    }
}
