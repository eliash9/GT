<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Santri;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SeleksiController extends Controller
{
    /**
     * Tampilkan halaman seleksi cepat (massal).
     */
    public function index(Request $request)
    {
        $query = Santri::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('kelas_formal', 'like', "%{$search}%");
            });
        }

        // Filter status seleksi
        if ($request->filled('status_seleksi')) {
            $query->where('status_seleksi', $request->status_seleksi);
        } else {
            // Default filter hanya tampilkan yang belum diproses atau lolos awal
            $query->whereIn('status_seleksi', ['Belum Diproses', 'Lolos Tahap Awal']);
        }

        // Tampilkan semua data tanpa pagination agar mudah seleksi massal, atau bisa pakai limit besar
        $santris = $query->orderBy('nama')->get();

        return Inertia::render('Seleksi/Index', [
            'santris' => $santris,
            'filters' => $request->only(['search', 'status_seleksi']),
        ]);
    }

    /**
     * Update massal hasil seleksi.
     */
    public function massUpdate(Request $request)
    {
        $validated = $request->validate([
            'santris' => 'required|array|min:1',
            'santris.*.id' => 'required|exists:santris,id',
            'santris.*.status_kelulusan' => 'required|in:Belum Lulus,Lulus,Tidak Lulus',
            'santris.*.status_seleksi' => 'required|in:Belum Diproses,Lolos Tahap Awal,Lolos Tahap Akhir,Tidak Lolos',
            // Opsional nilai
            'santris.*.akademisi' => 'nullable|in:A,B,C',
            'santris.*.marhalah_alquran' => 'nullable|in:A,B,C',
            'santris.*.haliyah_keaktifan' => 'nullable|in:A,B,C',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['santris'] as $data) {
                // Aturan wajib lulus untuk lolos akhir
                if ($data['status_seleksi'] === 'Lolos Tahap Akhir' && $data['status_kelulusan'] !== 'Lulus') {
                    $data['status_seleksi'] = 'Belum Diproses';
                }

                Santri::where('id', $data['id'])->update([
                    'status_kelulusan' => $data['status_kelulusan'],
                    'status_seleksi' => $data['status_seleksi'],
                    'akademisi' => $data['akademisi'] ?? null,
                    'marhalah_alquran' => $data['marhalah_alquran'] ?? null,
                    'haliyah_keaktifan' => $data['haliyah_keaktifan'] ?? null,
                ]);
            }
            DB::commit();

            return redirect()->back()->with('success', 'Data seleksi santri berhasil diperbarui massal!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data seleksi: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan rekap hasil seleksi.
     */
    public function rekap(Request $request)
    {
        // Statistik seleksi
        $stats_seleksi = Santri::select('status_seleksi', DB::raw('count(*) as total'))
            ->groupBy('status_seleksi')
            ->get()
            ->pluck('total', 'status_seleksi')
            ->toArray();

        // Statistik kelulusan
        $stats_kelulusan = Santri::select('status_kelulusan', DB::raw('count(*) as total'))
            ->groupBy('status_kelulusan')
            ->get()
            ->pluck('total', 'status_kelulusan')
            ->toArray();

        // Top 10 Peringkat Terbaik (berdasarkan rata-rata praktek dan akademisi A)
        // Aturan: Harus Lolos Tahap Akhir / Lulus
        $top_santris = Santri::with('skills')
            ->where('status_kelulusan', 'Lulus')
            ->where('status_seleksi', 'Lolos Tahap Akhir')
            ->get()
            ->sortByDesc('rata_rata_nilai_praktek')
            ->take(10)
            ->values();

        return Inertia::render('Seleksi/Rekap', [
            'stats_seleksi' => [
                'Lolos Tahap Akhir' => $stats_seleksi['Lolos Tahap Akhir'] ?? 0,
                'Lolos Tahap Awal' => $stats_seleksi['Lolos Tahap Awal'] ?? 0,
                'Tidak Lolos' => $stats_seleksi['Tidak Lolos'] ?? 0,
                'Belum Diproses' => $stats_seleksi['Belum Diproses'] ?? 0,
            ],
            'stats_kelulusan' => [
                'Lulus' => $stats_kelulusan['Lulus'] ?? 0,
                'Tidak Lulus' => $stats_kelulusan['Tidak Lulus'] ?? 0,
                'Belum Lulus' => $stats_kelulusan['Belum Lulus'] ?? 0,
            ],
            'top_santris' => $top_santris,
        ]);
    }
}
