<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use Modules\User\Infrastructure\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Handle attendance check-in via QR Scan.
     * Supports: 
     * 1. Admin scan User (Admin device scans user personal QR)
     * 2. User scan Session (User device scans session QR)
     */
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'session_qr' => 'required|string',
            'user_qr'    => 'nullable|string', // Required if Admin is scanning User
            'lat'        => 'nullable|string',
            'lng'        => 'nullable|string',
        ]);

        // 1. Find the Session
        $session = AttendanceSession::where('qr_code', $validated['session_qr'])->first();
        if (!$session) {
            return response()->json(['message' => 'Sesi absensi tidak valid.'], 404);
        }

        if ($session->status !== 'open') {
            return response()->json(['message' => 'Sesi absensi sudah ditutup.'], 400);
        }

        $attendant = null;
        $method = 'user_scan_session';

        // 2. Identify the Attendant
        if ($request->filled('user_qr')) {
            // Admin is scanning User
            $attendant = User::where('personal_qr_token', $request->user_qr)->first();
            if (!$attendant) {
                return response()->json(['message' => 'QR Code pengguna tidak dikenali.'], 404);
            }
            $method = 'admin_scan_user';
        } else {
            // User is scanning Session
            $attendant = auth()->user();
        }

        if (!$attendant) {
            return response()->json(['message' => 'Pengguna tidak terautentikasi.'], 401);
        }

        // 3. Permission Check
        $isGt = $attendant->hasRole('gt');
        $isPjgt = $attendant->hasRole(['pjgt', 'korwil']);

        if ($isGt && !$session->allow_gt) {
            return response()->json(['message' => 'GT tidak diperbolehkan absen pada sesi ini.'], 403);
        }
        if ($isPjgt && !$session->allow_pjgt) {
            return response()->json(['message' => 'PJGT/Korwil tidak diperbolehkan absen pada sesi ini.'], 403);
        }

        // 4. Check for duplicate attendance
        $exists = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $attendant->id)
            ->exists();
        
        if ($exists) {
            return response()->json([
                'message' => 'Anda (atau pengguna ini) sudah melakukan absensi untuk sesi ini.',
                'user' => $attendant->name
            ], 422);
        }

        // 5. Calculate Status (Present/Late)
        $status = 'present';
        // Add logic for late if session start_at is defined and current time > start_at + buffer
        // if ($session->start_at && now()->isAfter($session->start_at->addMinutes(15))) { $status = 'late'; }

        // 6. Record Attendance
        $attendance = Attendance::create([
            'attendance_session_id' => $session->id,
            'user_id' => $attendant->id,
            'scanned_at' => now(),
            'method' => $method,
            'status' => $status,
            'location_lat' => $request->lat,
            'location_lng' => $request->lng,
            'device_info' => $request->header('User-Agent'),
        ]);

        return response()->json([
            'message' => 'Absensi berhasil dicatat!',
            'user' => $attendant->name,
            'time' => $attendance->scanned_at->format('H:i:s'),
            'type' => $attendant->getRoleNames()->first()
        ]);
    }
}
