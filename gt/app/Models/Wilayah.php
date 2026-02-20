<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $fillable = ['nama', 'pjgt_id'];

    public function pjgt()
    {
        return $this->belongsTo(Pjgt::class);
    }

    public function lembagas()
    {
        return $this->hasMany(Lembaga::class);
    }
}
