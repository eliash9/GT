<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .summary { margin-top: 20px; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        @if($session)
            <h3>Sesi: {{ $session->title }}</h3>
            <p>Tipe: {{ strtoupper($session->type) }} | Tanggal: {{ $session->created_at->format('d F Y') }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peserta</th>
                <th>Role</th>
                <th>Lembaga</th>
                <th>Waktu</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $att)
                @php
                    $role = $att->user->getRoleNames()->first();
                    $lembaga = '-';
                    if ($role === 'gt') {
                        $lembaga = $att->user->santri->penugasanAktif->lembaga->nama ?? '-';
                    } elseif (in_array($role, ['pjgt', 'korwil'])) {
                        $lembaga = $att->user->pjgt->lembagas->count() . ' Lembaga';
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $att->user->name }}</td>
                    <td>{{ strtoupper($role) }}</td>
                    <td>{{ $lembaga }}</td>
                    <td>{{ $att->scanned_at->format('H:i:s') }}</td>
                    <td>{{ $att->status === 'present' ? 'Hadir' : 'Telat' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        Total Kehadiran: {{ $attendances->count() }} orang
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | Aplikasi PSM UGT
    </div>
</body>
</html>
