<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LembagaKebutuhan extends Model
{
    protected $table = 'lembaga_kebutuhan';

    protected $fillable = [
        'lembaga_id',
        'skill_id',
        'prioritas',
        'kuota',
        'keterangan',
    ];

    protected $casts = [
        'kuota' => 'integer',
    ];

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Bobot skor berdasarkan prioritas
     */
    public function getBobotAttribute(): int
    {
        return match ($this->prioritas) {
            'wajib'       => 10,
            'diutamakan'  => 5,
            'opsional'    => 2,
            default       => 0,
        };
    }
}
