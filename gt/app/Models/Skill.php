<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    protected $table = 'skill';

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Santri yang memiliki skill ini
     */
    public function santris(): BelongsToMany
    {
        return $this->belongsToMany(Santri::class, 'santri_skill')
            ->withPivot(['level', 'keterangan'])
            ->withTimestamps();
    }

    /**
     * Lembaga yang membutuhkan skill ini
     */
    public function lembagaKebutuhans(): HasMany
    {
        return $this->hasMany(LembagaKebutuhan::class);
    }

    /**
     * Label kategori
     */
    public function getLabelKategoriAttribute(): string
    {
        return match ($this->kategori) {
            'ilmu'       => 'Ilmu Agama',
            'hafalan'    => 'Hafalan',
            'seni'       => 'Seni',
            'organisasi' => 'Organisasi',
            'teknologi'  => 'Teknologi',
            default      => 'Lainnya',
        };
    }
}
