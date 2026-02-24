<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pjgt extends Model
{
    protected $fillable = ['id_pjgt', 'nama', 'no_hp', 'user_id'];

    public function user()
    {
        return $this->belongsTo(\Modules\User\Infrastructure\Models\User::class);
    }

    public function wilayahs()
    {
        return $this->hasMany(Wilayah::class);
    }

    public function lembagas()
    {
        return $this->hasMany(Lembaga::class);
    }
}
