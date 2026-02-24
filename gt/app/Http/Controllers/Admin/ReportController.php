<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportQuestion;
use App\Models\ReportAnswer;
use App\Models\Santri;
use App\Models\Pjgt;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['creator', 'reporterGt', 'reporterPjgt', 'santri', 'pjgt', 'lembaga']);

        // Filter / Search
        if ($request->has('search') && $request->search != '') {
             $search = $request->search;
             $query->where(function($q) use ($search) {
                  $q->whereHas('reporterGt', function($sq) use ($search) {
                       $sq->where('nama', 'like', "%{$search}%");
                  })->orWhereHas('reporterPjgt', function($sq) use ($search) {
                       $sq->where('nama', 'like', "%{$search}%");
                  })->orWhereHas('lembaga', function($sq) use ($search) {
                       $sq->where('nama', 'like', "%{$search}%");
                  });
             });
        }

        if ($request->has('type') && in_array($request->type, ['gt', 'pjgt', 'korwil'])) {
             $query->where('report_type', $request->type);
        }

        if ($request->has('status') && in_array($request->status, ['draft', 'submitted', 'evaluated'])) {
             $query->where('status', $request->status);
        }

        if ($request->has('month') && $request->month != '') {
             $query->where('period_month', $request->month);
        }

        if ($request->has('year') && $request->year != '') {
             $query->where('period_year', $request->year);
        }

        // Handle Export to Excel
        if ($request->has('export') && $request->export === 'excel') {
            if (!$request->has('type') || !in_array($request->type, ['gt', 'pjgt', 'korwil'])) {
                return back()->with('error', 'Pilih tipe laporan (GT / PJGT / Korwil) sebelum mengunduh rekap Excel!');
            }
            $filename = 'Rekap_Laporan_' . strtoupper($request->type) . '_' . date('Ymd_His') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\ReportsExport(
                    $request->type, 
                    $request->month, 
                    $request->year, 
                    $request->status
                ), 
                $filename
            );
        }

        // Summary Counts
        $summary = [
             'total' => Report::count(),
             'draft' => Report::where('status', 'draft')->count(),
             'submitted' => Report::where('status', 'submitted')->count(),
             'evaluated' => Report::where('status', 'evaluated')->count(),
        ];

        $reports = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
            
        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'filters' => $request->only(['search', 'type', 'status', 'month', 'year']),
            'summary' => $summary,
        ]);
    }

    public function create()
    {
        return Inertia::render('Reports/Create');
    }

    // API Endpoint for fetching targets when Admin picks a role
    public function getTargets(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'gt') {
            $targets = Santri::whereHas('penugasanAktif')
                ->with(['penugasanAktif.lembaga', 'penugasanAktif.lembaga.pjgt'])
                ->orderBy('nama')
                ->get()
                ->map(function ($gt) {
                    $p = $gt->penugasanAktif;
                    return [
                        'id' => $gt->id,
                        'name' => $gt->nama,
                        'contexts' => [[
                            'lembaga_id' => $p->lembaga_id,
                            'lembaga_name' => $p->lembaga->nama ?? '',
                            'pjgt_id' => $p->lembaga->pjgt_id ?? null,
                            'pjgt_name' => $p->lembaga->pjgt->nama ?? '',
                            'santri_id' => $gt->id,
                            'santri_name' => $gt->nama,
                        ]]
                    ];
                });
        } elseif ($type === 'pjgt') {
            $targets = Pjgt::whereHas('lembagas.penugasans', function ($q) {
                $q->whereIn('status', ['aktif', 'disetujui']);
            })
                ->with(['lembagas.penugasans' => function ($q) {
                    $q->whereIn('status', ['aktif', 'disetujui'])->with('santri');
                }])
                ->orderBy('nama')
                ->get()
                ->map(function ($pjgt) {
                    $contexts = [];
                    foreach ($pjgt->lembagas as $l) {
                        foreach ($l->penugasans as $p) {
                            $contexts[] = [
                                'lembaga_id' => $l->id,
                                'lembaga_name' => $l->nama,
                                'pjgt_id' => $pjgt->id,
                                'pjgt_name' => $pjgt->nama,
                                'santri_id' => $p->santri_id,
                                'santri_name' => $p->santri->nama ?? '',
                            ];
                        }
                    }
                    return [
                        'id' => $pjgt->id,
                        'name' => $pjgt->nama,
                        'contexts' => $contexts,
                    ];
                });
        } elseif ($type === 'korwil') {
            $targets = Pjgt::whereHas('wilayahs.lembagas.penugasans', function ($q) {
                $q->whereIn('status', ['aktif', 'disetujui']);
            })
                ->with(['wilayahs.lembagas.pjgt', 'wilayahs.lembagas.penugasans' => function ($q) {
                    $q->whereIn('status', ['aktif', 'disetujui'])->with('santri');
                }])
                ->orderBy('nama')
                ->get()
                ->map(function ($pjgt) {
                    $contexts = [];
                    foreach ($pjgt->wilayahs as $w) {
                        foreach ($w->lembagas as $l) {
                            foreach ($l->penugasans as $p) {
                                $contexts[] = [
                                    'lembaga_id' => $l->id,
                                    'lembaga_name' => $l->nama . ' (Wil: ' . $w->nama . ')',
                                    'pjgt_id' => $l->pjgt_id,
                                    'pjgt_name' => $l->pjgt ? $l->pjgt->nama : 'N/A',
                                    'santri_id' => $p->santri_id,
                                    'santri_name' => $p->santri->nama ?? '',
                                ];
                            }
                        }
                    }
                    return [
                        'id' => $pjgt->id,
                        'name' => $pjgt->nama . ' (Korwil)',
                        'contexts' => $contexts,
                    ];
                });
        } else {
            $targets = [];
        }

        return response()->json($targets);
    }

    public function init(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:gt,pjgt,korwil',
            'reporter_id' => 'required|integer',
            'period_month' => 'required|integer|min:1|max:6',
            'period_year' => 'required|integer',
            'santri_id' => 'nullable|integer',
            'pjgt_id' => 'nullable|integer',
            'lembaga_id' => 'nullable|integer',
        ]);

        // Check if report exists
        $report = Report::firstOrCreate(
            [
                'report_type' => $validated['report_type'],
                'reporter_id' => $validated['reporter_id'],
                'period_month' => $validated['period_month'],
                'period_year' => $validated['period_year'],
                'santri_id' => $validated['santri_id'] ?? null,
                'lembaga_id' => $validated['lembaga_id'] ?? null,
                'pjgt_id' => $validated['pjgt_id'] ?? null,
            ],
            [
                'status' => 'draft',
                'created_by' => auth()->id()
            ]
        );

        return redirect()->route('reports.edit', $report->id);
    }

    public function edit(Report $report)
    {
        $report->load(['answers', 'santri', 'pjgt', 'lembaga']);

        // Fetch categories with questions for this type
        $categories = ReportCategory::with(['questions'])
            ->where('report_type', $report->report_type)
            ->orderBy('order')
            ->get();
            
        // Map answers for easy Vue binding { question_id: answer }
        $answers = $report->answers->pluck('answer', 'report_question_id');

        return Inertia::render('Reports/Form', [
            'report' => $report,
            'categories' => $categories,
            'existingAnswers' => $answers,
        ]);
    }

    public function update(Request $request, Report $report)
    {
        // $request->answers is an array [ question_id => value ]
        $data = $request->validate([
            'answers' => 'nullable|array',
            'status' => 'required|in:draft,submitted,evaluated'
        ]);

        $answers = $data['answers'] ?? [];

        foreach ($answers as $qId => $answerVal) {
            // Handle arrays (from checkboxes) converting to JSON/string
            if (is_array($answerVal)) {
                $answerVal = json_encode($answerVal);
            }

            ReportAnswer::updateOrCreate(
                [
                    'report_id' => $report->id,
                    'report_question_id' => $qId,
                ],
                [
                    'answer' => $answerVal
                ]
            );
        }

        $report->update(['status' => $data['status']]);

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil disimpan.');
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->back()->with('success', 'Laporan berhasil dihapus.');
    }

    public function analytics(Request $request)
    {
        return Inertia::render('Reports/Analytics');
    }

    public function analyticsData(Request $request)
    {
        $type = $request->input('type', 'gt');
        $month = $request->input('month');
        $year = $request->input('year');

        // Fetch categories with questions to populate the dropdown
        $categories = ReportCategory::with('questions')->where('report_type', $type)->orderBy('order')->get();

        $analytics = [];
        $questionDetails = null;

        if ($request->has('question_id') && $request->question_id) {
            $question = ReportQuestion::find($request->question_id);
            if ($question) {
                $questionDetails = $question;
                $query = Report::where('report_type', $type);
                
                if ($month) $query->where('period_month', $month);
                if ($year) $query->where('period_year', $year);

                $query->whereHas('answers', function($q) use ($question) {
                    $q->where('report_question_id', $question->id)->whereNotNull('answer')->where('answer', '!=', '');
                })->with(['answers' => function($q) use ($question) {
                    $q->where('report_question_id', $question->id);
                }, 'reporterGt', 'reporterPjgt', 'santri', 'pjgt', 'lembaga']);

                $reports = $query->get();

                if (in_array($question->type, ['select', 'radio'])) {
                    $counts = [];
                    foreach($reports as $r) {
                        $ans = $r->answers->first()->answer;
                        if (!$ans) continue;
                        $counts[$ans] = ($counts[$ans] ?? 0) + 1;
                    }
                    $analytics = ['type' => 'chart', 'data' => $counts];
                } elseif ($question->type === 'checkbox') {
                    $counts = [];
                    foreach($reports as $r) {
                        $ansRaw = $r->answers->first()->answer;
                        $arr = json_decode($ansRaw, true);
                        if(is_array($arr)) {
                            foreach($arr as $a) {
                                $counts[$a] = ($counts[$a] ?? 0) + 1;
                            }
                        }
                    }
                    $analytics = ['type' => 'chart', 'data' => $counts];
                } else {
                    $list = [];
                    foreach ($reports as $r) {
                        $context = [];
                        if ($r->lembaga_id) $context[] = $r->lembaga->nama;
                        if ($r->report_type !== 'gt' && $r->santri_id) $context[] = $r->santri->nama;
                        if ($r->report_type !== 'pjgt' && $r->pjgt_id) $context[] = $r->pjgt->nama;

                        $list[] = [
                            'reporter' => $r->reporter_name,
                            'context' => implode(' | ', $context),
                            'answer' => $r->answers->first()->answer
                        ];
                    }
                    $analytics = ['type' => 'list', 'data' => $list];
                }
            }
        }

        return response()->json([
            'categories' => $categories,
            'question' => $questionDetails,
            'analytics' => $analytics
        ]);
    }
}
