<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pjgt extends Model
{
    protected $fillable = ['id_pjgt', 'nama', 'no_hp', 'user_id'];

    protected static function booted()
    {
        static::saved(function ($pjgt) {
            if (!$pjgt->user_id) {
                // Remove non-numeric for login purpose
                $identifier = preg_replace('/[^0-9]/', '', $pjgt->no_hp);
                $user = \Modules\User\Infrastructure\Models\User::create([
                    'name' => $pjgt->nama,
                    'email' => $identifier . '@pjgt.com',
                    'password' => \Illuminate\Support\Facades\Hash::make($identifier),
                ]);
                $user->assignRole('pjgt');
                $pjgt->user_id = $user->id;
                $pjgt->saveQuietly();
            }
        });
    }

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
