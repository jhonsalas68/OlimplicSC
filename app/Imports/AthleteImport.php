<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class AthleteImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();
        
        $category = Category::where('nombre', $data['categoria'])->first();

        try {
            $fecha = $data['fecha_de_nacimiento'] ? Carbon::createFromFormat('d/m/Y', $data['fecha_de_nacimiento']) : null;
        } catch (\Exception $e) {
            $fecha = null;
        }

        Athlete::updateOrCreate(
            ['ci' => $data['ci']],
            [
                'nombre'                  => $data['nombres'],
                'apellido_paterno'        => $data['apellido_paterno'],
                'apellido_materno'        => $data['apellido_materno'],
                'category_id'             => $category->id ?? null,
                'fecha_nacimiento'        => $fecha,
                'genero'                  => $data['genero'],
                'alergias'                => $data['alergias'] ?? null,
                'nombre_padre'            => $data['padretutor_nombre'] ?? null,
                'apellido_paterno_padre'  => $data['padretutor_ape_paterno'] ?? null,
                'apellido_materno_padre'  => $data['padretutor_ape_materno'] ?? null,
                'telefono_padre'          => $data['padretutor_telefono'] ?? null,
                'habilitado_booleano'     => (strtoupper($data['habilitado'] ?? '') === 'SÍ'),
            ]
        );
    }
}
