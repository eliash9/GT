<?php

namespace App\Exports;

use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportsExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $type, $month, $year, $status, $categories;

    public function __construct($type, $month, $year, $status)
    {
        $this->type = $type;
        $this->month = $month;
        $this->year = $year;
        $this->status = $status;

        // Fetch categories and questions for headings
        $this->categories = ReportCategory::with(['questions'])
            ->where('report_type', $type)
            ->orderBy('order')
            ->get();
    }

    public function view(): View
    {
        $query = Report::with(['creator', 'reporterGt', 'reporterPjgt', 'santri', 'pjgt', 'lembaga', 'answers'])
            ->where('report_type', $this->type);

        if ($this->month) {
            $query->where('period_month', $this->month);
        }
        if ($this->year) {
            $query->where('period_year', $this->year);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        return view('exports.reports', [
            'reports' => $reports,
            'categories' => $this->categories,
            'reportType' => $this->type
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
