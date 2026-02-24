<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\User\Infrastructure\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Kredensial yang Anda berikan salah.'
            ], 422);
        }

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => $user->load('roles')
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('roles');
        
        // Link to Santri or Pjgt if exists
        $santri = \App\Models\Santri::where('user_id', $user->id)->with('penugasanAktif.lembaga')->first();
        $pjgt = \App\Models\Pjgt::where('user_id', $user->id)->first();

        return response()->json([
            'user' => $user,
            'details' => [
                'is_gt' => $user->hasRole('gt'),
                'is_pjgt' => $user->hasRole('pjgt'),
                'is_korwil' => $user->hasRole('korwil'),
                'santri' => $santri,
                'pjgt' => $pjgt,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
