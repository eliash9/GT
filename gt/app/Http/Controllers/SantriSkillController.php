<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Skill;
use Illuminate\Http\Request;

/**
 * Mengelola skill yang dimiliki seorang santri (via AJAX / Inertia partial)
 */
class SantriSkillController extends Controller
{
    /**
     * Tambah / update skill pada santri
     */
    public function store(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'skill_id'    => 'required|exists:skill,id',
            'level'       => 'required|in:dasar,menengah,mahir',
            'keterangan'  => 'nullable|string|max:500',
        ]);

        $santri->skills()->syncWithoutDetaching([
            $validated['skill_id'] => [
                'level'      => $validated['level'],
                'keterangan' => $validated['keterangan'] ?? null,
            ],
        ]);

        return back()->with('success', 'Skill berhasil ditambahkan.');
    }

    /**
     * Update level / keterangan skill tertentu
     */
    public function update(Request $request, Santri $santri, Skill $skill)
    {
        $validated = $request->validate([
            'level'      => 'required|in:dasar,menengah,mahir',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $santri->skills()->updateExistingPivot($skill->id, $validated);

        return back()->with('success', 'Skill berhasil diperbarui.');
    }

    /**
     * Hapus skill dari santri
     */
    public function destroy(Santri $santri, Skill $skill)
    {
        $santri->skills()->detach($skill->id);

        return back()->with('success', 'Skill berhasil dihapus dari santri.');
    }
}
