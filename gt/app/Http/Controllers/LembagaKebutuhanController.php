<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use App\Models\LembagaKebutuhan;
use App\Models\Skill;
use Illuminate\Http\Request;

/**
 * Mengelola kebutuhan skill suatu lembaga
 */
class LembagaKebutuhanController extends Controller
{
    public function store(Request $request, Lembaga $lembaga)
    {
        $validated = $request->validate([
            'skill_id'   => 'required|exists:skill,id',
            'prioritas'  => 'required|in:wajib,diutamakan,opsional',
            'kuota'      => 'required|integer|min:1|max:99',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Cegah duplikat
        $existing = $lembaga->kebutuhans()->where('skill_id', $validated['skill_id'])->first();
        if ($existing) {
            $existing->update($validated);
        } else {
            $lembaga->kebutuhans()->create($validated);
        }

        return back()->with('success', 'Kebutuhan skill berhasil disimpan.');
    }

    public function update(Request $request, Lembaga $lembaga, LembagaKebutuhan $kebutuhan)
    {
        $validated = $request->validate([
            'prioritas'  => 'required|in:wajib,diutamakan,opsional',
            'kuota'      => 'required|integer|min:1|max:99',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $kebutuhan->update($validated);

        return back()->with('success', 'Kebutuhan skill berhasil diperbarui.');
    }

    public function destroy(Lembaga $lembaga, LembagaKebutuhan $kebutuhan)
    {
        $kebutuhan->delete();

        return back()->with('success', 'Kebutuhan skill berhasil dihapus.');
    }
}
