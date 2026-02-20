<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'pjgt_id'
    ];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function pjgt()
    {
        return $this->belongsTo(Pjgt::class);
    }
}
