<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tipe Laporan</th>
            <th>Nama Pelapor</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Lembaga Tugas</th>
            <th>PJGT Lembaga</th>
            <th>Target GT</th>
            <th>Status Laporan</th>
            @foreach($categories as $category)
                @foreach($category->questions as $question)
                    <th>[{{ $category->name }}] {{ $question->question }}</th>
                @endforeach
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($reports as $index => $report)
            @php
                $periods = [
                    1 => 'Syawal & Dzulqo\'dah',
                    2 => 'Dzulhijjah & Muharram',
                    3 => 'Safar & Rabi\'ul Awal',
                    4 => 'Rabi\'ul Akhir & Jumadil Awal',
                    5 => 'Jumadil Akhir & Rajab',
                    6 => 'Sya\'ban'
                ];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-transform: uppercase;">{{ $report->report_type }}</td>
                <td>{{ $report->reporter_name }}</td>
                <td>{{ $periods[$report->period_month] ?? $report->period_month }}</td>
                <td>{{ $report->period_year }} H</td>
                <td>{{ $report->lembaga ? $report->lembaga->nama : '-' }}</td>
                <td>{{ $report->pjgt ? $report->pjgt->nama : '-' }}</td>
                <td>{{ $report->santri ? $report->santri->nama : '-' }}</td>
                <td>{{ $report->status === 'submitted' ? 'Terkirim' : ($report->status === 'evaluated' ? 'Dievaluasi' : 'Draft') }}</td>
                @foreach($categories as $category)
                    @foreach($category->questions as $question)
                        @php
                            $answerRecord = $report->answers->firstWhere('report_question_id', $question->id);
                            $answerText = $answerRecord ? $answerRecord->answer : '-';
                            
                            // Handling array type string if using json format
                            if ($question->type === 'checkbox') {
                                try {
                                    $decoded = json_decode($answerText, true);
                                    if (is_array($decoded)) {
                                        $answerText = implode(", ", $decoded);
                                    }
                                } catch(\Exception $e) {}
                            }
                        @endphp
                        <td>{{ $answerText }}</td>
                    @endforeach
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
