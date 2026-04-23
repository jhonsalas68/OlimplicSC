<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    // Habilitamos timestamps pero solo para created_at
    public $timestamps = true;
    const UPDATED_AT = null;
    
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'properties',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * El usuario que realizó la acción.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
