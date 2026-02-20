<?php

namespace Modules\Auth\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Modules\User\Presentation\Requests\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
        }

        $request->fulfill();

        return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }
}
