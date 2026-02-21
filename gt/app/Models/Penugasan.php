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
        'santri_id',
        'lembaga_id',
        'tahun_psm_id',
        'skor_kecocokan',
        'status',
        'catatan',
        'disetujui_oleh',
        'disetujui_pada',
    ];

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
