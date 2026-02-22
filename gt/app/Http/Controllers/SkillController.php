<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SkillController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view skills')->only(['index', 'show']);
        $this->middleware('permission:create skills')->only(['create', 'store']);
        $this->middleware('permission:edit skills')->only(['edit', 'update']);
        $this->middleware('permission:delete skills')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Skill::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('kategori', 'like', "%{$request->search}%");
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $skills = $query->withCount(['santris', 'lembagaKebutuhans'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Skill/Index', [
            'skills'     => $skills,
            'filters'    => $request->only(['search', 'kategori']),
            'kategoris'  => ['ilmu', 'hafalan', 'seni', 'organisasi', 'teknologi', 'lainnya'],
        ]);
    }

    public function create()
    {
        return Inertia::render('Skill/Create', [
            'kategoris' => ['ilmu', 'hafalan', 'seni', 'organisasi', 'teknologi', 'lainnya'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255|unique:skill,nama',
            'kategori'  => 'required|in:ilmu,hafalan,seni,organisasi,teknologi,lainnya',
            'deskripsi' => 'nullable|string',
            'aktif'     => 'boolean',
        ]);

        $skill = Skill::create($validated);

        return redirect()->route('skills.index')
            ->with('success', 'Skill berhasil ditambahkan.');
    }

    public function show(Skill $skill)
    {
        $skill->loadCount(['santris', 'lembagaKebutuhans']);
        $skill->load(['santris' => fn($q) => $q->limit(10), 'lembagaKebutuhans.lembaga']);

        return Inertia::render('Skill/Show', [
            'skill' => $skill,
        ]);
    }

    public function edit(Skill $skill)
    {
        return Inertia::render('Skill/Edit', [
            'skill'     => $skill,
            'kategoris' => ['ilmu', 'hafalan', 'seni', 'organisasi', 'teknologi', 'lainnya'],
        ]);
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255|unique:skill,nama,' . $skill->id,
            'kategori'  => 'required|in:ilmu,hafalan,seni,organisasi,teknologi,lainnya',
            'deskripsi' => 'nullable|string',
            'aktif'     => 'boolean',
        ]);

        $skill->update($validated);

        return redirect()->route('skills.index')
            ->with('success', 'Skill berhasil diperbarui.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return redirect()->route('skills.index')
            ->with('success', 'Skill berhasil dihapus.');
    }
}
