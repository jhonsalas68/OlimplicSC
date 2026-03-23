<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class AthleteImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // El 'id_alfanumerico_unico' se genera en el model booted()
        // La categoría se busca por nombre
        $category = Category::where('nombre', $row['categoria'])->first();

        try {
            $fecha = $row['fecha_de_nacimiento'] ? Carbon::createFromFormat('d/m/Y', $row['fecha_de_nacimiento']) : null;
        } catch (\Exception $e) {
            $fecha = null;
        }

        return new Athlete([
            'nombre' => $row['nombres'],
            'apellido_paterno' => $row['apellido_paterno'],
            'apellido_materno' => $row['apellido_materno'],
            'ci' => $row['ci'],
            'category_id' => $category->id ?? null,
            'fecha_nacimiento' => $fecha,
            'genero' => $row['genero'],
            'alergias' => $row['alergias'] ?? null,
            'seguro_medico' => $row['seguro_medico'] ?? null,
            'nombre_padre' => $row['padretutor_nombre'] ?? null,
            'apellido_paterno_padre' => $row['padretutor_ape_paterno'] ?? null,
            'apellido_materno_padre' => $row['padretutor_ape_materno'] ?? null,
            'telefono_padre' => $row['padretutor_telefono'] ?? null,
            'habilitado_booleano' => (strtoupper($row['habilitado'] ?? '') === 'SÍ'),
        ]);
    }
}
