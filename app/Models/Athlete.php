<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Athlete extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'ci',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'foto',
        'fecha_nacimiento',
        'habilitado_booleano',
        'id_alfanumerico_unico',
        'contactos_padres',
        'alergias',
        'genero',
        // Seguro médico
        'tiene_seguro',
        'seguro_compania',
        'seguro_contacto',
        // Contacto para menores (padre/madre/tutor)
        'nombre_padre',
        'apellido_paterno_padre',
        'apellido_materno_padre',
        'telefono_padre',
        'relacion_contacto',
        // Contacto para mayores de edad
        'contacto_nombre',
        'contacto_telefono',
        'contacto_relacion',
    ];

    protected $casts = [
        'fecha_nacimiento'   => 'date',
        'habilitado_booleano' => 'boolean',
        'tiene_seguro'       => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** Calcula la edad actual en años cumplidos */
    public function edadActual(): int
    {
        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    /** Asigna la categoría correcta según la fecha de nacimiento */
    public function asignarCategoriaPorEdad(): void
    {
        $categoria = Category::resolverPorEdad($this->edadActual());
        $this->category_id = $categoria->id;
    }

    protected static function booted(): void
    {
        // Auto-asignar categoría antes de crear
        static::creating(function (Athlete $athlete) {
            if ($athlete->fecha_nacimiento) {
                $athlete->asignarCategoriaPorEdad();
            }

            if (empty($athlete->id_alfanumerico_unico)) {
                // Iniciales: primera letra de nombre, apellido paterno y materno
                $iniciales = strtoupper(
                    substr($athlete->nombre ?? '', 0, 1) .
                    substr($athlete->apellido_paterno ?? '', 0, 1) .
                    substr($athlete->apellido_materno ?? '', 0, 1)
                );
                if (strlen($iniciales) < 2) {
                    $iniciales = strtoupper(substr($athlete->nombre, 0, 3));
                }

                // Siguiente número correlativo con 5 dígitos
                $ultimo = static::max('id') ?? 0;
                $numero = str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);

                $athlete->id_alfanumerico_unico = $iniciales . '-' . $numero;
            }
        });

        // Recalcular categoría si cambia la fecha de nacimiento
        static::updating(function (Athlete $athlete) {
            if ($athlete->isDirty('fecha_nacimiento') && $athlete->fecha_nacimiento) {
                $athlete->asignarCategoriaPorEdad();
            }
        });
    }
}
