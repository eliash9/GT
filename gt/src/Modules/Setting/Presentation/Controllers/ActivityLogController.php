<?php

namespace Modules\Setting\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(): Response
    {
        $activities = Activity::with('causer')->latest()->paginate(20);

        return Inertia::render('Settings/ActivityLog', [
            'activities' => $activities,
        ]);
    }
}
