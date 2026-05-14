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
        $catName = trim($data['categoria'] ?? $data['categoría'] ?? $data['category'] ?? '');
        $category = $catName ? Category::where('nombre', 'like', $catName)->first() : null;

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

        // Buscamos los nombres y el CI
        $nombres = $data['nombres'] ?? $data['nombre'] ?? '';
        
        // El slug de "C.I." suele ser "ci" o "c_i"
        $ci = $data['ci'] ?? $data['c_i'] ?? $data['cedula'] ?? $data['ci_atleta'] ?? null;

        if (!$ci) return; // No podemos importar sin CI

        // Normalizamos valores booleanos (SI/NO)
        $isHabilitado = in_array(strtoupper(trim($data['habilitado'] ?? '')), ['SÍ', 'SI', '1', 'YES']);
        $tieneSeguro  = in_array(strtoupper(trim($data['tiene_seguro'] ?? '')), ['SÍ', 'SI', '1', 'YES']);

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
                'habilitado_booleano'     => $isHabilitado,

                // Seguro médico
                'tiene_seguro'            => $tieneSeguro,
                'seguro_compania'         => $data['aseguradora'] ?? $data['seguro_compania'] ?? null,
                'seguro_contacto'         => $data['telefono_seguro'] ?? $data['teléfono_seguro'] ?? null,

                // Contacto Tutor (Padre/Madre)
                'relacion_contacto'       => $data['tutor_relacion'] ?? $data['tutor_relación'] ?? null,
                'nombre_padre'            => $data['tutor_nombres'] ?? $data['padretutor_nombre'] ?? null,
                'apellido_paterno_padre'  => $data['tutor_ape_paterno'] ?? $data['padretutor_ape_paterno'] ?? null,
                'apellido_materno_padre'  => $data['tutor_ape_materno'] ?? $data['padretutor_ape_materno'] ?? null,
                'telefono_padre'          => $data['tutor_telefono'] ?? $data['teléfono_tutor'] ?? null,

                // Contacto Emergencia
                'contacto_nombre'         => $data['contacto_emergencia'] ?? $data['contacto_nombre'] ?? null,
                'contacto_telefono'       => $data['contacto_telefono'] ?? $data['teléfono_contacto'] ?? null,
                'contacto_relacion'       => $data['contacto_relacion'] ?? null,
            ]
        );
    }
}
