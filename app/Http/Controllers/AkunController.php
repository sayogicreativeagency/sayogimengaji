<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/** Masuk, daftar, dan keluar dengan email + password. Google/OTP menyusul. */
class AkunController extends Controller
{
    private const MAKS_PERCOBAAN = 5;

    public function formMasuk()
    {
        return view('akun.masuk');
    }

    public function masuk(Request $request)
    {
        $kredensial = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $kunci = Str::lower($kredensial['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($kunci).' detik.',
            ]);
        }

        if (! Auth::attempt($kredensial, $request->boolean('ingat'))) {
            RateLimiter::hit($kunci, 60);

            throw ValidationException::withMessages(['email' => 'Email atau password salah.']);
        }

        RateLimiter::clear($kunci);
        $request->session()->regenerate();

        if (! $request->user()->peran->bisaMasukAdmin()) {
            // Jangan kembalikan pengguna biasa ke /admin yang tadi ia buka: hasilnya hanya 403.
            if (str_starts_with(parse_url($request->session()->get('url.intended', ''), PHP_URL_PATH) ?? '', '/admin')) {
                $request->session()->forget('url.intended');
            }

            return redirect()->intended('/');
        }

        return redirect()->intended('/admin');
    }

    public function formDaftar()
    {
        return view('akun.daftar');
    }

    public function daftar(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Hanya kolom ini yang diisi: peran selalu default (pengguna).
        $user = User::create(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']]);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/');
    }

    public function keluar(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
