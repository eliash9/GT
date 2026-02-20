<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\Pjgt;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $query = Wilayah::query()->with('pjgt');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%");
        }

        $wilayahs = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Wilayah/Index', [
            'wilayahs' => $wilayahs,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        $pjgts = Pjgt::all();
        return Inertia::render('Wilayah/Create', [
            'pjgts' => $pjgts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'pjgt_id' => 'nullable|exists:pjgts,id',
        ]);

        Wilayah::create($validated);

        return redirect()->route('wilayahs.index')->with('success', 'Data Wilayah berhasil ditambahkan.');
    }

    public function edit(Wilayah $wilayah)
    {
        $pjgts = Pjgt::all();
        return Inertia::render('Wilayah/Edit', [
            'wilayah' => $wilayah,
            'pjgts' => $pjgts
        ]);
    }

    public function update(Request $request, Wilayah $wilayah)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'pjgt_id' => 'nullable|exists:pjgts,id',
        ]);

        $wilayah->update($validated);

        return redirect()->route('wilayahs.index')->with('success', 'Data Wilayah berhasil diperbarui.');
    }

    public function destroy(Wilayah $wilayah)
    {
        $wilayah->delete();

        return redirect()->route('wilayahs.index')->with('success', 'Data Wilayah berhasil dihapus.');
    }
}
