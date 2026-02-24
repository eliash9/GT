<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReportCategory;
use Inertia\Inertia;

class ReportCategoryController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'gt');
        $categories = ReportCategory::with('questions')
            ->where('report_type', $type)
            ->orderBy('order')
            ->get();

        return Inertia::render('ReportMaster/Index', [
            'categories' => $categories,
            'currentType' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'report_type' => 'required|in:gt,pjgt,korwil',
            'order' => 'integer',
        ]);

        ReportCategory::create($validated);

        return redirect()->back()->with('success', 'Kategori laporan berhasil ditambahkan.');
    }

    public function update(Request $request, ReportCategory $reportCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'report_type' => 'required|in:gt,pjgt,korwil',
            'order' => 'integer',
        ]);

        $reportCategory->update($validated);

        return redirect()->back()->with('success', 'Kategori laporan berhasil diupdate.');
    }

    public function destroy(ReportCategory $reportCategory)
    {
        $reportCategory->delete();

        return redirect()->back()->with('success', 'Kategori laporan berhasil dihapus.');
    }
}
