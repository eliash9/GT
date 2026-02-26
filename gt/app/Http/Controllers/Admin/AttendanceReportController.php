<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $sessions = AttendanceSession::orderBy('created_at', 'desc')->get();
        
        $query = Attendance::with([
            'user.roles',
            'user.santri.penugasanAktif.lembaga',
            'user.pjgt.lembagas',
            'session'
        ])->latest();

        if ($request->filled('session_id')) {
            $query->where('attendance_session_id', $request->session_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->whereHas('user.roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $attendances = $query->paginate(15)->withQueryString();

        return Inertia::render('Attendance/Reports/Index', [
            'attendances' => $attendances,
            'sessions' => $sessions,
            'filters' => $request->all(),
        ]);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new AttendanceExport($request->all()), 'laporan-absensi-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Attendance::with([
            'user.roles',
            'user.santri.penugasanAktif.lembaga',
            'user.pjgt.lembagas',
            'session'
        ]);

        if ($request->filled('session_id')) {
            $query->where('attendance_session_id', $request->session_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->whereHas('user.roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $data = $query->get();
        $session = $request->filled('session_id') ? AttendanceSession::find($request->session_id) : null;

        $pdf = Pdf::loadView('pdf.attendance', [
            'attendances' => $data,
            'session' => $session,
            'title' => 'Laporan Absensi Kehadiran'
        ]);

        return $pdf->download('laporan-absensi-' . now()->format('Y-m-d') . '.pdf');
    }
}
