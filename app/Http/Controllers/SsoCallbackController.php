<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SsoTokenVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SsoCallbackController extends Controller
{
    public function callback(Request $request, SsoTokenVerifier $verifier)
    {
        if ($request->query('sso') === 'none') {
            return redirect('/login');
        }

        $token = $request->query('token');

        if (!$token) {
            abort(400, 'Missing SSO token.');
        }

        try {
            $claims = $verifier->verify($token);
        } catch (\Exception $e) {
            abort(403, 'SSO verification failed: ' . $e->getMessage());
        }

        $user = User::firstOrCreate(
            ['email' => $claims->email],
            [
                'name' => $claims->name,
                'password' => Hash::make(Str::random(32)),
                'role' => $claims->role ?? 'user',
            ]
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/');
    }
}
