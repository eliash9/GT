<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportQuestion;
use App\Models\ReportAnswer;
use App\Models\Santri;
use App\Models\Pjgt;
use App\Models\Wilayah;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller
{
    public function questions(Request $request)
    {
        $user = $request->user();
        $type = 'gt';
        
        if ($user->hasRole('korwil')) {
            $type = 'korwil';
        } elseif ($user->hasRole('pjgt')) {
            $type = 'pjgt';
        }

        $categories = ReportCategory::with(['questions'])
            ->where('report_type', $type)
            ->orderBy('order')
            ->get();

        return response()->json($categories);
    }

    public function history(Request $request)
    {
        $user = $request->user();
        $santri = Santri::where('user_id', $user->id)->first();
        $pjgt = Pjgt::where('user_id', $user->id)->first();

        $query = Report::query()->orderBy('created_at', 'desc');

        if ($santri) {
            $query->where('report_type', 'gt')->where('reporter_id', $santri->id);
        } elseif ($pjgt) {
            // Can be pjgt or korwil
            $query->whereIn('report_type', ['pjgt', 'korwil'])->where('reporter_id', $pjgt->id);
        } else {
            // Admin or other
            $query->where('created_by', $user->id);
        }

        $reports = $query->with(['santri', 'pjgt', 'lembaga'])->get();

        return response()->json($reports);
    }

    public function checkReport(Request $request)
    {
        $request->validate([
            'period_month' => 'required|integer',
            'period_year' => 'required|integer',
        ]);

        $user = $request->user();
        $santri = Santri::where('user_id', $user->id)->first();
        $pjgt = Pjgt::where('user_id', $user->id)->first();

        // Type determination
        if ($user->hasRole('korwil')) {
            $type = 'korwil';
            $reporterId = $pjgt->id ?? null;
        } elseif ($user->hasRole('pjgt')) {
            $type = 'pjgt';
            $reporterId = $pjgt->id ?? null;
        } else {
            $type = 'gt';
            $reporterId = $santri->id ?? null;
        }

        if (!$reporterId) {
            return response()->json(null);
        }

        $report = Report::where('report_type', $type)
            ->where('reporter_id', $reporterId)
            ->where('period_month', $request->period_month)
            ->where('period_year', $request->period_year)
            ->with(['answers'])
            ->first();

        // Contexts for dropdown (helpful for PJGT/Korwil who manage many institutions)
        $contexts = [];
        if ($type === 'gt' && $santri) {
            $p = $santri->penugasanAktif;
            if ($p) {
                $contexts[] = [
                    'lembaga_id' => $p->lembaga_id,
                    'lembaga_name' => $p->lembaga->nama ?? '',
                    'pjgt_id' => $p->lembaga->pjgt_id ?? null,
                    'santri_id' => $santri->id,
                ];
            }
        } elseif ($type === 'pjgt' && $pjgt) {
            foreach ($pjgt->lembagas as $l) {
                foreach ($l->penugasans()->whereIn('status', ['disetujui', 'aktif'])->get() as $p) {
                    $contexts[] = [
                        'lembaga_id' => $l->id,
                        'lembaga_name' => $l->nama,
                        'pjgt_id' => $pjgt->id,
                        'santri_id' => $p->santri_id,
                        'santri_name' => $p->santri->nama ?? '',
                    ];
                }
            }
        } elseif ($type === 'korwil' && $pjgt) {
            // Assume korwil role implies checking their wilayahs
            foreach ($pjgt->wilayahs as $w) {
                foreach ($w->lembagas as $l) {
                    foreach ($l->penugasans()->whereIn('status', ['disetujui', 'aktif'])->get() as $p) {
                        $contexts[] = [
                            'lembaga_id' => $l->id,
                            'lembaga_name' => $l->nama . " ($w->nama)",
                            'pjgt_id' => $l->pjgt_id,
                            'santri_id' => $p->santri_id,
                            'santri_name' => $p->santri->nama ?? '',
                        ];
                    }
                }
            }
        }

        if ($report) {
            $answers = $report->answers->pluck('answer', 'report_question_id');
            return response()->json([
                'report' => $report,
                'answers' => $answers,
                'available_contexts' => $contexts
            ]);
        }

        // If not found by specific context yet, check if there's a request for specific context
        if ($request->has('santri_id') && $request->has('lembaga_id')) {
            $report = Report::where('report_type', $type)
                ->where('reporter_id', $reporterId)
                ->where('period_month', $request->period_month)
                ->where('period_year', $request->period_year)
                ->where('santri_id', $request->santri_id)
                ->where('lembaga_id', $request->lembaga_id)
                ->with(['answers'])
                ->first();
            
            if ($report) {
                $answers = $report->answers->pluck('answer', 'report_question_id');
                return response()->json([
                    'report' => $report,
                    'answers' => $answers,
                    'available_contexts' => $contexts
                ]);
            }
        }

        return response()->json([
            'available_contexts' => $contexts
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'period_month' => 'required|integer',
            'period_year' => 'required|integer',
            'answers' => 'required|array',
        ]);

        $user = $request->user();
        $type = 'gt';
        $reporterId = null;
        $santriId = null;
        $pjgtId = null;
        $lembagaId = null;

        $santri = Santri::where('user_id', $user->id)->first();
        $pjgt = Pjgt::where('user_id', $user->id)->first();

        if ($user->hasRole('korwil')) {
            $type = 'korwil';
            $reporterId = $pjgt->id ?? null;
        } elseif ($user->hasRole('pjgt')) {
            $type = 'pjgt';
            $reporterId = $pjgt->id ?? null;
        } else {
            $type = 'gt';
            $reporterId = $santri->id ?? null;
        }

        if (!$reporterId) {
            return response()->json(['message' => 'Profil GT/PJGT/Korwil tidak ditemukan untuk akun Anda.'], 403);
        }

        $santriId = $request->santri_id;
        $pjgtId = $request->pjgt_id;
        $lembagaId = $request->lembaga_id;

        // Context Auto-Setup if not provided
        if (!$santriId || !$pjgtId || !$lembagaId) {
            if ($type === 'gt' && $santri) {
                $p = $santri->penugasanAktif;
                if ($p) {
                    $santriId = $santri->id;
                    $pjgtId = $p->lembaga->pjgt_id ?? null;
                    $lembagaId = $p->lembaga_id;
                }
            } elseif (($type === 'pjgt' || $type === 'korwil') && $pjgt) {
                if ($type === 'pjgt') {
                    $firstLembaga = $pjgt->lembagas->first();
                    if ($firstLembaga) {
                        $lembagaId = $firstLembaga->id;
                        $pjgtId = $pjgt->id;
                        $firstPenugasan = $firstLembaga->penugasans()->whereIn('status', ['disetujui', 'aktif'])->first();
                        $santriId = $firstPenugasan->santri_id ?? null;
                    }
                } else {
                    foreach ($pjgt->wilayahs as $w) {
                        foreach ($w->lembagas as $l) {
                            $p = $l->penugasans()->whereIn('status', ['disetujui', 'aktif'])->first();
                            if ($p) {
                                $lembagaId = $l->id;
                                $pjgtId = $l->pjgt_id;
                                $santriId = $p->santri_id;
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        if (!$santriId || !$pjgtId || !$lembagaId) {
            return response()->json(['message' => 'Gagal menentukan konteks penugasan (Lembaga/GT/PJGT). Pastikan Anda memilih target yang akan dilaporkan.'], 422);
        }

        // Check for existing report with FULL context
        $existing = Report::where('report_type', $type)
            ->where('reporter_id', $reporterId)
            ->where('period_month', $request->period_month)
            ->where('period_year', $request->period_year)
            ->where('santri_id', $santriId)
            ->where('lembaga_id', $lembagaId)
            ->first();

        if ($existing && $existing->status === 'evaluated') {
            return response()->json(['message' => 'Laporan periode ini sudah dievaluasi dan tidak dapat diubah.'], 422);
        }

        DB::beginTransaction();
        try {
            $report = Report::updateOrCreate(
                [
                    'report_type' => $type,
                    'reporter_id' => $reporterId,
                    'period_month' => $request->period_month,
                    'period_year' => $request->period_year,
                    'santri_id' => $santriId,
                    'lembaga_id' => $lembagaId,
                ],
                [
                    'status' => 'submitted',
                    'created_by' => $user->id,
                    'pjgt_id' => $pjgtId,
                ]
            );

            // Re-fetch context if we used updateOrCreate logic but variables were null (not possible if penugasanAktif exists, but for safety)

            foreach ($request->answers as $qId => $val) {
                if (is_array($val)) {
                    $val = json_encode($val);
                }
                ReportAnswer::updateOrCreate(
                    [
                        'report_id' => $report->id,
                        'report_question_id' => $qId,
                    ],
                    [
                        'answer' => $val
                    ]
                );
            }

            DB::commit();
            return response()->json(['message' => 'Laporan berhasil disimpan!', 'report' => $report]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan laporan: ' . $e->getMessage()], 500);
        }
    }
}
