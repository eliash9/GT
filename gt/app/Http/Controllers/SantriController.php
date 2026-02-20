<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function index()
    {
        $santris = \App\Models\Santri::latest()->get();
        return inertia('Santri/Index', [
            'santris' => $santris
        ]);
    }

    public function create()
    {
        return inertia('Santri/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:santris,nis',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat_lengkap' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'angkatan' => 'required|integer',
            'status_tugas' => 'required|in:Menunggu,Sedang Bertugas,Selesai',
        ]);

        \App\Models\Santri::create($validated);

        return redirect()->route('santris.index')->with('success', 'Data santri berhasil ditambahkan.');
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
            'nis' => 'required|string|unique:santris,nis,' . $santri->id,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat_lengkap' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'angkatan' => 'required|integer',
            'status_tugas' => 'required|in:Menunggu,Sedang Bertugas,Selesai',
        ]);

        $santri->update($validated);

        return redirect()->route('santris.index')->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy(\App\Models\Santri $santri)
    {
        $santri->delete();

        return redirect()->route('santris.index')->with('success', 'Data santri berhasil dihapus.');
    }
}
