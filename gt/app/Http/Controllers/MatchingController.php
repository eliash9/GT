<?php

namespace App\Http\Controllers;

use App\Models\LembagaKebutuhan;
use App\Models\Lembaga;
use App\Models\Santri;
use App\Models\Skill;
use App\Models\Penugasan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MatchingController extends Controller
{
    /**
     * Halaman utama matching: pilih lembaga dan lihat rekomendasi santri
     */
    public function index(Request $request)
    {
        $lembagaId = $request->get('lembaga_id');
        $wilayahId = $request->get('wilayah_id');

        $lembagasQuery = Lembaga::with(['wilayah', 'kebutuhans.skill'])
            ->withCount([
                'penugasans as penugasan_aktif_count' => fn($q) => $q->whereIn('status', ['disetujui', 'aktif'])
            ])
            ->where('status', 'aktif');

        if ($wilayahId) {
            $lembagasQuery->where('wilayah_id', $wilayahId);
        }

        $lembagas = $lembagasQuery->orderBy('nama')->get();

        $rekomendasi = [];

        if ($lembagaId) {
            $lembaga = Lembaga::with(['wilayah', 'kebutuhans.skill'])->findOrFail($lembagaId);
            $rekomendasi = $this->hitungRekomendasi($lembaga);
        }

        return Inertia::render('Matching/Index', [
            'lembagas'         => $lembagas,
            'selectedLembaga'  => $lembagaId ? Lembaga::with(['wilayah', 'kebutuhans.skill'])->find($lembagaId) : null,
            'rekomendasi'      => $rekomendasi,
            'wilayahs'         => \App\Models\Wilayah::orderBy('nama')->get(),
            'filters'          => $request->only(['lembaga_id', 'wilayah_id']),
        ]);
    }

    /**
     * Hitung skor kecocokan setiap santri terhadap kebutuhan lembaga
     */
    private function hitungRekomendasi(Lembaga $lembaga): array
    {
        $kebutuhans = $lembaga->kebutuhans()->with('skill')->get();

        if ($kebutuhans->isEmpty()) {
            return [];
        }

        // ID lembaga ini berdasarkan wilayah (filter wilayah)
        $wilayahId = $lembaga->wilayah_id;

        // Santri yang belum punya penugasan aktif
        $santris = Santri::with(['skills'])
            ->where('status_tugas', 'Menunggu')
            ->whereDoesntHave('penugasanAktif')
            ->get();

        $hasil = [];

        foreach ($santris as $santri) {
            $santriSkillIds = $santri->skills->pluck('id')->toArray();
            $skor            = 0;
            $terpenuhi       = [];
            $belumTerpenuhi  = [];

            foreach ($kebutuhans as $kebutuhan) {
                $bobot = match ($kebutuhan->prioritas) {
                    'wajib'      => 10,
                    'diutamakan' => 5,
                    'opsional'   => 2,
                    default      => 0,
                };

                if (in_array($kebutuhan->skill_id, $santriSkillIds)) {
                    $skor        += $bobot;
                    $terpenuhi[]  = $kebutuhan->skill->nama;
                } else {
                    if ($kebutuhan->prioritas === 'wajib') {
                        $belumTerpenuhi[] = ['nama' => $kebutuhan->skill->nama, 'prioritas' => 'wajib'];
                    } else {
                        $belumTerpenuhi[] = ['nama' => $kebutuhan->skill->nama, 'prioritas' => $kebutuhan->prioritas];
                    }
                }
            }

            // Hanya tampilkan santri yang setidaknya cocok 1 skill
            if ($skor > 0) {
                $hasil[] = [
                    'santri'          => $santri->only(['id', 'nis', 'nama', 'kelas', 'angkatan', 'jenis_kelamin']),
                    'skor'            => $skor,
                    'terpenuhi'       => $terpenuhi,
                    'belum_terpenuhi' => $belumTerpenuhi,
                    'pct'             => min(100, round($skor / max(1, $kebutuhans->sum(fn($k) => match ($k->prioritas) {
                        'wajib' => 10, 'diutamakan' => 5, 'opsional' => 2, default => 0
                    })) * 100)),
                ];
            }
        }

        // Urutkan dari skor tertinggi
        usort($hasil, fn($a, $b) => $b['skor'] <=> $a['skor']);

        return $hasil;
    }

    /**
     * Assign langsung dari halaman matching (simpan sebagai penugasan diusulkan)
     */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'santri_id'  => 'required|exists:santris,id',
            'lembaga_id' => 'required|exists:lembagas,id',
            'skor'       => 'nullable|integer|min:0',
        ]);

        // Cek apakah santri sudah punya penugasan aktif
        $santri = Santri::findOrFail($validated['santri_id']);
        if ($santri->penugasanAktif) {
            return back()->with('error', "Santri {$santri->nama} sudah memiliki penugasan aktif.");
        }

        Penugasan::create([
            'santri_id'       => $validated['santri_id'],
            'lembaga_id'      => $validated['lembaga_id'],
            'skor_kecocokan'  => $validated['skor'] ?? 0,
            'status'          => 'diusulkan',
        ]);

        // Tandai status santri
        $santri->update(['status_tugas' => 'Sedang Bertugas']);

        return redirect()->route('penugasans.index')
            ->with('success', "Penugasan berhasil diusulkan untuk {$santri->nama}.");
    }
}
