<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Attendance::with([
            'user.roles',
            'user.santri.penugasanAktif.lembaga',
            'user.pjgt.lembagas',
            'session'
        ]);

        if (isset($this->filters['session_id']) && $this->filters['session_id'] !== '') {
            $query->where('attendance_session_id', $this->filters['session_id']);
        }

        if (isset($this->filters['status']) && $this->filters['status'] !== '') {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['role']) && $this->filters['role'] !== '') {
            $query->whereHas('user.roles', function($q) {
                $q->where('name', $this->filters['role']);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Sesi Absensi',
            'Nama Lengkap',
            'Role',
            'Lembaga Penugasan / Tanggung Jawab',
            'Waktu Scan',
            'Metode',
            'Status',
            'Lokasi (Lat, Lng)',
        ];
    }

    public function map($row): array
    {
        $role = $row->user->getRoleNames()->first();
        $lembaga = '-';

        if ($role === 'gt') {
            $lembaga = $row->user->santri->penugasanAktif->lembaga->nama ?? 'Belum ada penugasan';
        } elseif (in_array($role, ['pjgt', 'korwil'])) {
            $lembagas = $row->user->pjgt->lembagas->pluck('nama')->toArray();
            $lembaga = count($lembagas) > 0 ? implode(', ', $lembagas) : 'Tidak ada lembaga';
        }

        return [
            $row->id,
            $row->session->title,
            $row->user->name,
            strtoupper($role),
            $lembaga,
            $row->scanned_at->format('d/m/Y H:i:s'),
            $row->method === 'admin_scan_user' ? 'Admin Scan' : 'User Scan',
            $row->status === 'present' ? 'Hadir' : 'Terlambat',
            $row->location_lat ? $row->location_lat . ', ' . $row->location_lng : '-',
        ];
    }
}
