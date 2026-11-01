<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function create(Request $request)
    {
        return view('kontak', ['q' => (string) $request->query('q')]);
    }

    public function store(Request $request)
    {
        // Honeypot: kolom tersembunyi yang hanya diisi bot.
        if ($request->filled('situs')) {
            return redirect('/kontak')->with('status', 'Terima kasih, laporanmu sudah kami terima.');
        }

        Laporan::create($request->validate([
            'nama' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'q' => ['nullable', 'string', 'max:200'],
            'pesan' => ['required', 'string', 'min:5', 'max:5000'],
        ]));

        return redirect('/kontak')->with('status', 'Terima kasih, laporanmu sudah kami terima.');
    }
}
