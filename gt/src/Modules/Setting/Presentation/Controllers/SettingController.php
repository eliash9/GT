<?php

namespace Modules\Setting\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Setting\Infrastructure\Models\Setting;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view settings')->only(['index']);
        $this->middleware('permission:edit settings')->only(['update']);
    }

    public function index(): Response
    {
        $settings = Setting::all()->groupBy('group');

        return Inertia::render('Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => 'nullable',
        ]);

        foreach ($request->settings as $item) {
            $value = $item['value'];
            
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                // Determine file name, could be the key to overwrite
                $filename = $item['key'] . '.' . $value->getClientOriginalExtension();
                $path = $value->storeAs('settings', $filename, 'public');
                $value = '/storage/' . $path; // Or you could just save the path
            } elseif ($value === null && \Modules\Setting\Infrastructure\Models\Setting::where('key', $item['key'])->value('type') === 'image') {
                // Don't overwrite image with null if they didn't upload a new one
                continue;
            }

            Setting::set($item['key'], $value ?? '');
        }

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }
}