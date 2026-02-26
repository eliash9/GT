<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AttendanceSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = AttendanceSession::withCount('attendances')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        return Inertia::render('Attendance/Sessions/Index', [
            'sessions' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Attendance/Sessions/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:departure,meeting,event,other',
            'allow_gt' => 'boolean',
            'allow_pjgt' => 'boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['qr_code'] = Str::random(12);
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'open';

        $session = AttendanceSession::create($validated);

        return redirect()->route('attendance-sessions.show', $session->id)
            ->with('success', 'Sesi absensi berhasil dibuat.');
    }

    public function show(AttendanceSession $attendanceSession)
    {
        $attendanceSession->load(['creator', 'attendances.user']);
        
        return Inertia::render('Attendance/Sessions/Show', [
            'session' => $attendanceSession,
            'attendances' => $attendanceSession->attendances
        ]);
    }

    public function edit(AttendanceSession $attendanceSession)
    {
        return Inertia::render('Attendance/Sessions/Edit', [
            'session' => $attendanceSession
        ]);
    }

    public function update(Request $request, AttendanceSession $attendanceSession)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:departure,meeting,event,other',
            'allow_gt' => 'boolean',
            'allow_pjgt' => 'boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'status' => 'required|in:open,closed',
        ]);

        $attendanceSession->update($validated);

        return redirect()->route('attendance-sessions.show', $attendanceSession->id)
            ->with('success', 'Sesi absensi berhasil diperbarui.');
    }

    public function destroy(AttendanceSession $attendanceSession)
    {
        $attendanceSession->delete();

        return redirect()->route('attendance-sessions.index')
            ->with('success', 'Sesi absensi berhasil dihapus.');
    }

    public function toggleStatus(AttendanceSession $attendanceSession)
    {
        $attendanceSession->update([
            'status' => $attendanceSession->status === 'open' ? 'closed' : 'open'
        ]);

        return back()->with('success', 'Status sesi berhasil diubah.');
    }

    public function scanner(AttendanceSession $attendanceSession)
    {
        return Inertia::render('Attendance/Sessions/Scanner', [
            'session' => $attendanceSession
        ]);
    }
}
