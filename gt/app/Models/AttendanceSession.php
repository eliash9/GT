<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'status',
        'allow_gt',
        'allow_pjgt',
        'qr_code',
        'start_at',
        'end_at',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'allow_gt' => 'boolean',
        'allow_pjgt' => 'boolean',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function creator()
    {
        return $this->belongsTo(\Modules\User\Infrastructure\Models\User::class, 'created_by');
    }
}
