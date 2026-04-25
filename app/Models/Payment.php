<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'monto',
        'mes_correspondiente',
        'estado_pago',
        'concepto',
        'descripcion',
        'cobrado_por',
        'metodo_pago',
        'whatsapp_number',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->external_id)) {
                $model->external_id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function cobrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cobrado_por');
    }
}
