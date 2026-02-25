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
            'email' => 'required', // This will accept email, NIS, or No HP
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $identifier = $request->email;
        $user = null;

        // Try as Email
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
        } else {
            // Try as NIS (Santri)
            $santri = \App\Models\Santri::where('nis', $identifier)->first();
            if ($santri && $santri->user_id) {
                $user = User::find($santri->user_id);
            } else {
                // Try as No HP (PJGT) - clean numeric first
                $cleanPhone = preg_replace('/[^0-9]/', '', $identifier);
                $pjgt = \App\Models\Pjgt::where('no_hp', 'LIKE', '%' . $cleanPhone . '%')->first();
                if ($pjgt && $pjgt->user_id) {
                    $user = User::find($pjgt->user_id);
                }
            }
        }

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
        
        $santri = \App\Models\Santri::where('user_id', $user->id)
            ->with(['penugasanAktif.lembaga.pjgt'])
            ->first();
            
        $pjgt = \App\Models\Pjgt::where('user_id', $user->id)
            ->with([
                'lembagas.santriAktif.santri',
                'wilayahs.lembagas.santriAktif.santri'
            ])
            ->first();

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
