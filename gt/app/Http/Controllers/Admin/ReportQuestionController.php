<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReportQuestion;

class ReportQuestionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_category_id' => 'required|exists:report_categories,id',
            'question' => 'required|string',
            'type' => 'required|in:text,textarea,radio,checkbox,select',
            // if options are passed as comma separated string or array, handle validation
            // assuming vue sends array directly:
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'order' => 'integer',
        ]);

        ReportQuestion::create($validated);

        return redirect()->back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function update(Request $request, ReportQuestion $reportQuestion)
    {
        $validated = $request->validate([
            'report_category_id' => 'required|exists:report_categories,id',
            'question' => 'required|string',
            'type' => 'required|in:text,textarea,radio,checkbox,select',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'order' => 'integer',
        ]);

        $reportQuestion->update($validated);

        return redirect()->back()->with('success', 'Pertanyaan berhasil diupdate.');
    }

    public function destroy(ReportQuestion $reportQuestion)
    {
        $reportQuestion->delete();

        return redirect()->back()->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
