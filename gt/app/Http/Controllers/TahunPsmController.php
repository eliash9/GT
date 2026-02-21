<?php

namespace App\Http\Controllers;

use App\Models\TahunPsm;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TahunPsmController extends Controller
{
    public function index(Request $request)
    {
        $query = TahunPsm::query();

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $tahunPsms = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('TahunPsm/Index', [
            'tahunPsms' => $tahunPsms,
            'filters'   => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('TahunPsm/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'                   => 'required|string|max:255',
            'tanggal_mulai_masehi'    => 'nullable|date',
            'tanggal_selesai_masehi'  => 'nullable|date|after_or_equal:tanggal_mulai_masehi',
            'tanggal_mulai_hijriah'   => 'nullable|string|max:255',
            'tanggal_selesai_hijriah' => 'nullable|string|max:255',
            'aktif'                   => 'boolean',
        ]);

        if (!empty($validated['aktif'])) {
            TahunPsm::where('aktif', true)->update(['aktif' => false]);
        }

        TahunPsm::create($validated);

        return redirect()->route('tahun-psm.index')
            ->with('success', 'Master Tahun PSM berhasil ditambahkan.');
    }

    public function show(TahunPsm $tahunPsm)
    {
        // Redirect to edit as we likely don't need a complex show page for this simple master data
        return redirect()->route('tahun-psm.edit', $tahunPsm->id);
    }

    public function edit(TahunPsm $tahunPsm)
    {
        return Inertia::render('TahunPsm/Edit', [
            'tahunPsm' => $tahunPsm,
        ]);
    }

    public function update(Request $request, TahunPsm $tahunPsm)
    {
        $validated = $request->validate([
            'judul'                   => 'required|string|max:255',
            'tanggal_mulai_masehi'    => 'nullable|date',
            'tanggal_selesai_masehi'  => 'nullable|date|after_or_equal:tanggal_mulai_masehi',
            'tanggal_mulai_hijriah'   => 'nullable|string|max:255',
            'tanggal_selesai_hijriah' => 'nullable|string|max:255',
            'aktif'                   => 'boolean',
        ]);

        if (!empty($validated['aktif'])) {
            TahunPsm::where('id', '!=', $tahunPsm->id)->where('aktif', true)->update(['aktif' => false]);
        }

        $tahunPsm->update($validated);

        return redirect()->route('tahun-psm.index')
            ->with('success', 'Master Tahun PSM berhasil diperbarui.');
    }

    public function destroy(TahunPsm $tahunPsm)
    {
        $tahunPsm->delete();

        return redirect()->route('tahun-psm.index')
            ->with('success', 'Tahun PSM berhasil dihapus.');
    }
}
