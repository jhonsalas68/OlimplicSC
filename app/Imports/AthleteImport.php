<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Carbon;

class AthleteImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        // Convertimos todo a minúsculas para que sea fácil de buscar
        $data = array_change_key_case($row->toArray(), CASE_LOWER);
        
        // Buscamos la categoría de forma flexible
        $catName = $data['categoria'] ?? $data['categoría'] ?? $data['category'] ?? null;
        $category = $catName ? Category::where('nombre', 'ilike', trim($catName))->first() : null;

        // Buscamos la fecha de nacimiento
        $fechaRaw = $data['fecha_de_nacimiento'] ?? $data['fecha_nacimiento'] ?? $data['nacimiento'] ?? null;
        try {
            if ($fechaRaw) {
                // Si viene de Excel como objeto de fecha o string d/m/Y
                $fecha = is_numeric($fechaRaw) 
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaRaw)
                    : Carbon::createFromFormat('d/m/Y', $fechaRaw);
            } else {
                $fecha = null;
            }
        } catch (\Exception $e) {
            $fecha = null;
        }

        // Buscamos los nombres y apellidos
        $nombres = $data['nombres'] ?? $data['nombre'] ?? '';
        $ci = $data['ci'] ?? $data['cedula'] ?? $data['ci_atleta'] ?? null;

        if (!$ci) return; // No podemos importar sin CI

        Athlete::updateOrCreate(
            ['ci' => $ci],
            [
                'nombre'                  => $nombres,
                'apellido_paterno'        => $data['apellido_paterno'] ?? $data['paterno'] ?? null,
                'apellido_materno'        => $data['apellido_materno'] ?? $data['materno'] ?? null,
                'category_id'             => $category->id ?? null,
                'fecha_nacimiento'        => $fecha,
                'genero'                  => $data['genero'] ?? $data['género'] ?? 'Masculino',
                'alergias'                => $data['alergias'] ?? null,
                'nombre_padre'            => $data['padretutor_nombre'] ?? $data['nombre_padre'] ?? null,
                'apellido_paterno_padre'  => $data['padretutor_ape_paterno'] ?? $data['apellido_paterno_padre'] ?? null,
                'apellido_materno_padre'  => $data['padretutor_ape_materno'] ?? $data['apellido_materno_padre'] ?? null,
                'telefono_padre'          => $data['padretutor_telefono'] ?? $data['telefono_padre'] ?? null,
                'habilitado_booleano'     => (strtoupper($data['habilitado'] ?? '') === 'SÍ' || ($data['habilitado'] ?? '') == '1'),
            ]
        );
    }
}
