<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pjgt extends Model
{
    protected $fillable = ['nama', 'no_hp'];

    public function wilayahs()
    {
        return $this->hasMany(Wilayah::class);
    }

    public function lembagas()
    {
        return $this->hasMany(Lembaga::class);
    }
}
