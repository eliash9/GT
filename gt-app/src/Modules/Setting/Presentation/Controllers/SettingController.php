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
            Setting::set($item['key'], $item['value'] ?? '');
        }

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }
}