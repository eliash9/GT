<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Models\Santri;
use App\Models\Lembaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PenugasanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penugasan::with(['santri', 'lembaga.wilayah.pjgt', 'lembaga.pjgt', 'tahunPsm'])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('santri', fn($q) => $q->where('nama', 'like', "%$s%")
                ->orWhere('nis', 'like', "%$s%"))
                ->orWhereHas('lembaga', fn($q) => $q->where('nama', 'like', "%$s%"))
                ->orWhere('kode_tugas', 'like', "%$s%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('wilayah_id')) {
            $query->whereHas('lembaga', fn($q) => $q->where('wilayah_id', $request->wilayah_id));
        }

        $penugasans = $query->paginate(15)->withQueryString();

        return Inertia::render('Penugasan/Index', [
            'penugasans' => $penugasans,
            'filters'    => $request->only(['search', 'status', 'wilayah_id']),
            'wilayahs'   => \App\Models\Wilayah::orderBy('nama')->get(),
            'statuses'   => ['diusulkan', 'disetujui', 'aktif', 'selesai', 'dibatalkan'],
        ]);
    }

    public function create()
    {
        return Inertia::render('Penugasan/Create', [
            'santris'  => Santri::where('status_tugas', 'Menunggu')
                ->where('status_seleksi', 'Lolos Tahap Akhir')
                ->whereDoesntHave('penugasanAktif')
                ->orderBy('nama')
                ->get(['id', 'nis', 'nama', 'kelas', 'angkatan']),
            'lembagas' => Lembaga::with('wilayah')
                ->where('status', 'aktif')
                ->orderBy('nama')
                ->get(['id', 'nama', 'wilayah_id']),
            'tahunPsms' => \App\Models\TahunPsm::orderBy('tanggal_mulai_masehi', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id'       => 'required|exists:santris,id',
            'lembaga_id'      => 'required|exists:lembagas,id',
            'tahun_psm_id'    => 'nullable|exists:tahun_psms,id',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'catatan'         => 'nullable|string',
        ]);

        // Cek santri tidak punya penugasan aktif
        $santri = Santri::findOrFail($validated['santri_id']);
        if ($santri->penugasanAktif) {
            return back()->withErrors(['santri_id' => 'Santri ini sudah memiliki penugasan aktif.']);
        }

        $penugasan = Penugasan::create(array_merge($validated, ['status' => 'diusulkan']));

        return redirect()->route('penugasans.show', $penugasan->id)
            ->with('success', 'Penugasan berhasil dibuat.');
    }

    public function show(Penugasan $penugasan)
    {
        $penugasan->load(['santri.skills', 'lembaga.wilayah.pjgt', 'lembaga.pjgt', 'lembaga.kebutuhans.skill', 'disetujuiOleh', 'tahunPsm']);

        return Inertia::render('Penugasan/Show', [
            'penugasan' => $penugasan,
        ]);
    }

    public function edit(Penugasan $penugasan)
    {
        return Inertia::render('Penugasan/Edit', [
            'penugasan' => $penugasan->load(['santri', 'lembaga.wilayah', 'tahunPsm']),
            'lembagas'  => Lembaga::with('wilayah')
                ->where('status', 'aktif')
                ->orderBy('nama')
                ->get(['id', 'nama', 'wilayah_id']),
            'tahunPsms' => \App\Models\TahunPsm::orderBy('tanggal_mulai_masehi', 'desc')->get(),
        ]);
    }

    public function update(Request $request, Penugasan $penugasan)
    {
        $validated = $request->validate([
            'lembaga_id'      => 'required|exists:lembagas,id',
            'tahun_psm_id'    => 'nullable|exists:tahun_psms,id',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'catatan'         => 'nullable|string',
            'status'          => 'required|in:diusulkan,disetujui,aktif,selesai,dibatalkan',
        ]);

        $penugasan->update($validated);

        return redirect()->route('penugasans.show', $penugasan->id)
            ->with('success', 'Penugasan berhasil diperbarui.');
    }

    public function destroy(Penugasan $penugasan)
    {
        $penugasan->delete();

        return redirect()->route('penugasans.index')
            ->with('success', 'Penugasan berhasil dihapus.');
    }

    /**
     * Ubah status penugasan (approve, aktifkan, selesaikan, batalkan)
     */
    public function ubahStatus(Request $request, Penugasan $penugasan)
    {
        $request->validate([
            'status' => 'required|in:diusulkan,disetujui,aktif,selesai,dibatalkan',
        ]);

        $data = ['status' => $request->status];

        if (in_array($request->status, ['disetujui', 'aktif'])) {
            $data['disetujui_oleh'] = Auth::id();
            $data['disetujui_pada'] = now();
        }

        // Sinkron status santri
        $santri = $penugasan->santri;
        if ($request->status === 'aktif' || $request->status === 'disetujui') {
            $santri->update(['status_tugas' => 'Sedang Bertugas']);
        } elseif ($request->status === 'selesai') {
            $santri->update(['status_tugas' => 'Selesai']);
        } elseif ($request->status === 'dibatalkan') {
            $santri->update(['status_tugas' => 'Menunggu']);
        }

        $penugasan->update($data);

        return back()->with('success', 'Status penugasan berhasil diubah menjadi "' . $request->status . '".');
    }
}
