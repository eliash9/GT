<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class ApiTokenController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = $request->user()->createToken($request->name);

        return Redirect::back()->with('success', 'Token created successfully. Please copy it now, you won\'t be able to see it again!')->with('flash_token', $token->plainTextToken);
    }

    public function destroy(Request $request, $tokenId): RedirectResponse
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return Redirect::back()->with('success', 'Token deleted successfully.');
    }
}
