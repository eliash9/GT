<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lembaga extends Model
{
    protected $fillable = [
        'nama',
        'alamat',
        'latitude',
        'longitude',
        'urlmap',
        'status',
        'wilayah_id',
        'pjgt_id',
    ];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function pjgt(): BelongsTo
    {
        return $this->belongsTo(Pjgt::class);
    }

    /**
     * Kebutuhan skill lembaga ini
     */
    public function kebutuhans(): HasMany
    {
        return $this->hasMany(LembagaKebutuhan::class);
    }

    /**
     * Penugasan yang terhubung ke lembaga ini
     */
    public function penugasans(): HasMany
    {
        return $this->hasMany(Penugasan::class);
    }

    /**
     * Guru tugas yang sedang aktif di lembaga ini
     */
    public function santriAktif(): HasMany
    {
        return $this->hasMany(Penugasan::class)
            ->whereIn('status', ['disetujui', 'aktif'])
            ->with('santri');
    }
}
