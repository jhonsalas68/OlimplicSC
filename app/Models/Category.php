<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'edad_min',
        'edad_max',
    ];

    /**
     * Devuelve la categoría correcta según la edad cumplida.
     * Reglas: ≤13 Pre Infantil | ≤15 Infantil | ≤17 Menores | ≤19 Juvenil | 20+ Libre
     */
    public static function resolverPorEdad(int $edad): self
    {
        // Limitar la edad para evitar errores con fechas futuras o muy antiguas
        if ($edad < 0) $edad = 0;
        
        $categoria = static::where('edad_min', '<=', $edad)
            ->where('edad_max', '>=', $edad)
            ->orderBy('edad_min')
            ->first();

        if (!$categoria) {
            // Si por alguna razón extrema no la encuentra, asignar la de mayor rango (Libre)
            $categoria = static::orderBy('edad_max', 'desc')->firstOrFail();
        }

        return $categoria;
    }

    public function athletes(): HasMany
    {
        return $this->hasMany(Athlete::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }

    public function coaches(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Training::class, 'category_id', 'id', 'id', 'coach_id')->distinct();
    }
}
