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

    /**
     * Calcula si el atleta está al día con la mensualidad del mes actual.
     */
    public function isAlDia(): bool
    {
        $mesActual = now()->format('Y-m');
        return $this->payments()
            ->where('concepto', 'mensualidad')
            ->where('mes_correspondiente', $mesActual)
            ->where('estado_pago', 'pagado')
            ->exists();
    }

    /**
     * Scope para atletas que están al día (pago realizado en el mes actual).
     */
    public function scopeAlDia($query)
    {
        $mesActual = now()->format('Y-m');
        return $query->whereHas('payments', function ($q) use ($mesActual) {
            $q->where('concepto', 'mensualidad')
              ->where('mes_correspondiente', $mesActual)
              ->where('estado_pago', 'pagado');
        });
    }

    /**
     * Scope para atletas que deben (sin pago realizado en el mes actual).
     */
    public function scopeDebe($query)
    {
        $mesActual = now()->format('Y-m');
        return $query->whereDoesntHave('payments', function ($q) use ($mesActual) {
            $q->where('concepto', 'mensualidad')
              ->where('mes_correspondiente', $mesActual)
              ->where('estado_pago', 'pagado');
        });
    }

    protected $fillable = [
        'category_id',
        'ci',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'foto',
        'fecha_nacimiento',
        'habilitado_booleano',
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

    /** Relacion optima para obtener el ultimo pago y evitar N+1 */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /** Asigna la categoría correcta según la fecha de nacimiento */
    public function asignarCategoriaPorEdad(): void
    {
        $categoria = Category::resolverPorEdad($this->edadActual());
        $this->category_id = $categoria->id;
    }

    protected static function booted(): void
    {
        static::creating(function (Athlete $athlete) {
            if ($athlete->fecha_nacimiento) {
                $athlete->asignarCategoriaPorEdad();
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
