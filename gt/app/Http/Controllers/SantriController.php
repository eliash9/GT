<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Santri::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%")
                    ->orWhere('nama_ayah', 'like', "%{$search}%");
            });
        }

        $santris = $query->latest()->paginate(10)->withQueryString();

        return inertia('Santri/Index', [
            'santris' => $santris,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return inertia('Santri/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis'           => 'required|string|unique:santris,nis',
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir'  => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat_lengkap'=> 'nullable|string',
            'no_hp'         => 'nullable|string',
            'angkatan'      => 'required|integer',
            'status_tugas'  => 'required|in:Menunggu,Sedang Bertugas,Selesai',
            'kelas'         => 'nullable|string|max:255',
            'nama_ayah'     => 'nullable|string|max:255',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('santri_fotos', 'public');
        }

        $santri = \App\Models\Santri::create($validated);

        return redirect()->route('santris.show', $santri->id)
            ->with('success', 'Data santri berhasil ditambahkan.');
    }

    public function show(\App\Models\Santri $santri)
    {
        $santri->load(['skills', 'penugasanAktif.lembaga', 'penugasanAktif.tahunPsm']);
        
        return inertia('Santri/Show', [
            'santri' => $santri,
            'availableSkills' => \App\Models\Skill::where('aktif', true)->orderBy('nama')->get(),
        ]);
    }

    public function edit(\App\Models\Santri $santri)
    {
        return inertia('Santri/Edit', [
            'santri' => $santri
        ]);
    }

    public function update(Request $request, \App\Models\Santri $santri)
    {
        $validated = $request->validate([
            'nis'           => 'required|string|unique:santris,nis,' . $santri->id,
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir'  => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat_lengkap'=> 'nullable|string',
            'no_hp'         => 'nullable|string',
            'angkatan'      => 'required|integer',
            'status_tugas'  => 'required|in:Menunggu,Sedang Bertugas,Selesai',
            'kelas'         => 'nullable|string|max:255',
            'nama_ayah'     => 'nullable|string|max:255',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($santri->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($santri->foto);
            }
            $validated['foto'] = $request->file('foto')->store('santri_fotos', 'public');
        }

        $santri->update($validated);

        return redirect()->route('santris.show', $santri->id)
            ->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy(\App\Models\Santri $santri)
    {
        $santri->delete();

        return redirect()->route('santris.index')
            ->with('success', 'Data santri berhasil masuk ke keranjang sampah (soft delete).');
    }

    public function trash(Request $request)
    {
        $query = \App\Models\Santri::onlyTrashed();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%")
                    ->orWhere('nama_ayah', 'like', "%{$search}%");
            });
        }

        $santris = $query->latest()->paginate(10)->withQueryString();

        return inertia('Santri/Trash', [
            'santris' => $santris,
            'filters' => $request->only(['search']),
        ]);
    }

    public function restore($id)
    {
        $santri = \App\Models\Santri::onlyTrashed()->findOrFail($id);
        $santri->restore();

        return redirect()->route('santris.trash')
            ->with('success', 'Data santri berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $santri = \App\Models\Santri::onlyTrashed()->findOrFail($id);

        if ($santri->foto) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($santri->foto);
        }

        $santri->forceDelete();

        return redirect()->route('santris.trash')
            ->with('success', 'Data santri berhasil dihapus secara permanen.');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SantriExport, 'data_santri.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SantriImport, $request->file('file'));

        return redirect()->route('santris.index')
            ->with('success', 'Data Santri berhasil diimpor.');
    }
}
