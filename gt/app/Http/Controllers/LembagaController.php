<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use App\Models\Wilayah;
use App\Models\Pjgt;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LembagaController extends Controller
{
    public function index(Request $request)
    {
        $query = Lembaga::query()->with(['wilayah', 'pjgt']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
        }

        $lembagas = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Lembaga/Index', [
            'lembagas' => $lembagas,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Lembaga/Create', [
            'wilayahs' => Wilayah::all(),
            'pjgts' => Pjgt::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'urlmap' => 'nullable|string',
            'status' => 'required|in:aktif,non-aktif',
            'wilayah_id' => 'nullable|exists:wilayahs,id',
            'pjgt_id' => 'nullable|exists:pjgts,id',
        ]);

        Lembaga::create($validated);

        return redirect()->route('lembagas.index')->with('success', 'Data Lembaga berhasil ditambahkan.');
    }

    public function edit(Lembaga $lembaga)
    {
        return Inertia::render('Lembaga/Edit', [
            'lembaga' => $lembaga,
            'wilayahs' => Wilayah::all(),
            'pjgts' => Pjgt::all(),
        ]);
    }

    public function update(Request $request, Lembaga $lembaga)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'urlmap' => 'nullable|string',
            'status' => 'required|in:aktif,non-aktif',
            'wilayah_id' => 'nullable|exists:wilayahs,id',
            'pjgt_id' => 'nullable|exists:pjgts,id',
        ]);

        $lembaga->update($validated);

        return redirect()->route('lembagas.index')->with('success', 'Data Lembaga berhasil diperbarui.');
    }

    public function destroy(Lembaga $lembaga)
    {
        $lembaga->delete();

        return redirect()->route('lembagas.index')->with('success', 'Data Lembaga berhasil dihapus.');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LembagaExport, 'data_lembaga.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\LembagaImport, $request->file('file'));

        return redirect()->route('lembagas.index')->with('success', 'Data Lembaga berhasil diimpor.');
    }
}
