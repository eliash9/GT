<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunPsm extends Model
{
    protected $fillable = [
        'judul',
        'tanggal_mulai_masehi',
        'tanggal_selesai_masehi',
        'tanggal_mulai_hijriah',
        'tanggal_selesai_hijriah',
        'aktif',
    ];

    protected $casts = [
        'tanggal_mulai_masehi'   => 'date',
        'tanggal_selesai_masehi' => 'date',
        'aktif'                  => 'boolean',
    ];

    public function penugasans()
    {
        return $this->hasMany(Penugasan::class);
    }
}
