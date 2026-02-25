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
        'kamar',
        'nama_ayah',
        'haliyah_jabatan',
        'haliyah_keaktifan',
        'haliyah_pelanggaran',
        'akademisi',
        'kelas_formal',
        'nilai_ujian_tulis',
        'nilai_ujian_materi',
        'nilai_ujian_praktek_kelas',
        'marhalah_alquran',
        'status_seleksi',
        'status_kelulusan',
        'user_id'
    ];

    protected static function booted()
    {
        static::saved(function ($santri) {
            if (!$santri->user_id) {
                $user = \Modules\User\Infrastructure\Models\User::create([
                    'name' => $santri->nama,
                    'email' => $santri->nis . '@gt.com', 
                    'password' => \Illuminate\Support\Facades\Hash::make($santri->nis),
                ]);
                $user->assignRole('gt');
                $santri->user_id = $user->id;
                $santri->saveQuietly();
            }
        });
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Modules\User\Infrastructure\Models\User::class);
    }

    /**
     * @var array
     */
    protected $appends = [
        'jumlah_nilai_praktek',
        'rata_rata_nilai_praktek'
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
        return $this->hasMany(Penugasan::class)->latest();
    }

    /**
     * Penugasan aktif santri (hanya boleh 1)
     */
    public function penugasanAktif(): HasOne
    {
        return $this->hasOne(Penugasan::class)
            ->whereIn('status', ['diusulkan', 'disetujui', 'aktif']);
    }

    /**
     * Hitung Jumlah Nilai Praktek
     */
    public function getJumlahNilaiPraktekAttribute()
    {
        return ($this->nilai_ujian_tulis ?? 0) + ($this->nilai_ujian_materi ?? 0) + ($this->nilai_ujian_praktek_kelas ?? 0);
    }

    /**
     * Hitung Rata-rata Nilai Praktek
     */
    public function getRataRataNilaiPraktekAttribute()
    {
        $total = $this->jumlah_nilai_praktek;
        return $total > 0 ? round($total / 3, 2) : 0;
    }
}
