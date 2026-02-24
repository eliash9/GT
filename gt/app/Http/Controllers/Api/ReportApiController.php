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

        // Try to find context from active penugasan if any
        if ($type === 'gt' && $santri) {
            $p = $santri->penugasanAktif;
            if ($p) {
                $santriId = $santri->id;
                $pjgtId = $p->lembaga->pjgt_id ?? null;
                $lembagaId = $p->lembaga_id;
            }
        }

        // Actually, for PWA we should probably let user pick context if they have multiple, 
        // but for now let's use the same logic as admin init if possible.
        // Or simple version: just create the report.

        DB::beginTransaction();
        try {
            $report = Report::create([
                'report_type' => $type,
                'reporter_id' => $reporterId,
                'period_month' => $request->period_month,
                'period_year' => $request->period_year,
                'status' => 'submitted', // Auto submit from PWA
                'created_by' => $user->id,
                'santri_id' => $santriId,
                'pjgt_id' => $pjgtId,
                'lembaga_id' => $lembagaId,
            ]);

            foreach ($request->answers as $qId => $val) {
                if (is_array($val)) {
                    $val = json_encode($val);
                }
                ReportAnswer::create([
                    'report_id' => $report->id,
                    'report_question_id' => $qId,
                    'answer' => $val
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Laporan berhasil terkirim!', 'report' => $report]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan laporan: ' . $e->getMessage()], 500);
        }
    }
}
