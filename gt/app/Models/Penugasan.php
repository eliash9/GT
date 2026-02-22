<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Infrastructure\Models\User;

class Penugasan extends Model
{
    use SoftDeletes;

    protected $table = 'penugasan';

    protected $fillable = [
        'kode_tugas',
        'santri_id',
        'lembaga_id',
        'tahun_psm_id',
        'skor_kecocokan',
        'status',
        'catatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'disetujui_oleh',
        'disetujui_pada',
    ];

    /**
     * Boot the model to automatic setup attributes.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_tugas)) {
                $count = static::whereYear('created_at', date('Y'))
                               ->whereMonth('created_at', date('m'))
                               ->count() + 1;
                $model->kode_tugas = 'GT-' . date('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $casts = [
        'disetujui_pada'  => 'datetime',
        'skor_kecocokan'  => 'integer',
        'tahun_psm_id'    => 'integer',
    ];

    public function tahunPsm(): BelongsTo
    {
        return $this->belongsTo(TahunPsm::class);
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class);
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    /**
     * Label warna status untuk UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'diusulkan'  => 'yellow',
            'disetujui'  => 'blue',
            'aktif'      => 'green',
            'selesai'    => 'gray',
            'dibatalkan' => 'red',
            default      => 'gray',
        };
    }
}
