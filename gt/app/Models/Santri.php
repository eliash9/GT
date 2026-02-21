<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Santri extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nis',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat_lengkap',
        'no_hp',
        'angkatan',
        'status_tugas',
        'foto',
        'kelas',
        'nama_ayah'
    ];

    /**
     * Skill yang dimiliki santri
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'santri_skill')
            ->withPivot(['level', 'keterangan'])
            ->withTimestamps();
    }

    /**
     * Semua penugasan santri
     */
    public function penugasans(): HasMany
    {
        return $this->hasMany(Penugasan::class);
    }

    /**
     * Penugasan aktif santri (hanya boleh 1)
     */
    public function penugasanAktif(): HasOne
    {
        return $this->hasOne(Penugasan::class)
            ->whereIn('status', ['diusulkan', 'disetujui', 'aktif']);
    }
}
