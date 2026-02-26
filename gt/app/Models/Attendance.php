<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'attendance_session_id',
        'user_id',
        'scanned_at',
        'method',
        'status',
        'location_lat',
        'location_lng',
        'device_info',
        'notes',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function user()
    {
        return $this->belongsTo(\Modules\User\Infrastructure\Models\User::class);
    }
}
