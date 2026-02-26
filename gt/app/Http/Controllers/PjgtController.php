<?php

namespace App\Http\Controllers;

use App\Models\Pjgt;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PjgtController extends Controller
{
    public function index(Request $request)
    {
        $query = Pjgt::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('id_pjgt', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
        }

        $pjgts = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Pjgt/Index', [
            'pjgts'   => $pjgts,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Pjgt/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pjgt' => 'nullable|string|max:255|unique:pjgts,id_pjgt',
            'nama'    => 'required|string|max:255',
            'no_hp'   => 'nullable|string|max:50',
        ]);

        $pjgt = Pjgt::create($validated);

        return redirect()->route('pjgts.show', $pjgt->id)
            ->with('success', 'Data PJGT berhasil ditambahkan.');
    }

    public function show(Pjgt $pjgt)
    {
        $pjgt->load([
            'user.roles',
            'wilayahs',
            'wilayahs.lembagas',
            'lembagas',
            'lembagas.wilayah',
        ]);

        return Inertia::render('Pjgt/Show', [
            'pjgt' => $pjgt,
        ]);
    }

    public function edit(Pjgt $pjgt)
    {
        return Inertia::render('Pjgt/Edit', [
            'pjgt' => $pjgt,
        ]);
    }

    public function update(Request $request, Pjgt $pjgt)
    {
        $validated = $request->validate([
            'id_pjgt' => 'nullable|string|max:255|unique:pjgts,id_pjgt,' . $pjgt->id,
            'nama'    => 'required|string|max:255',
            'no_hp'   => 'nullable|string|max:50',
        ]);

        $pjgt->update($validated);

        return redirect()->route('pjgts.show', $pjgt->id)
            ->with('success', 'Data PJGT berhasil diperbarui.');
    }

    public function destroy(Pjgt $pjgt)
    {
        $pjgt->delete();

        return redirect()->route('pjgts.index')
            ->with('success', 'Data PJGT berhasil dihapus.');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PjgtExport, 'data_pjgt.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PjgtImport, $request->file('file'));

        return redirect()->route('pjgts.index')
            ->with('success', 'Data PJGT berhasil diimpor.');
    }
}
