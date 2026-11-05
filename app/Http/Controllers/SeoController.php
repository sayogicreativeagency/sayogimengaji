<?php

namespace App\Http\Controllers;

use App\Models\Doa;
use App\Models\Hadist;
use App\Models\Surat;

/** robots.txt dan sitemap.xml dibuat dinamis supaya URL-nya mengikuti domain yang dipakai (APP_URL/host). */
class SeoController extends Controller
{
    public function robots()
    {
        return response(implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /livewire',
            'Disallow: /cari/saring',
            '',
            'Sitemap: '.url('/sitemap.xml'),
            '',
        ]))->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /** Satu berkas cukup: ±36.800 URL, di bawah batas 50.000 per sitemap. */
    public function sitemap()
    {
        return response()->view('sitemap', [
            // /tentang sengaja tidak dimasukkan; hasil /cari dan halaman akun noindex.
            'halaman' => ['/', '/hadist', '/quran', '/doa', '/kalender', '/dukung', '/kontak', '/sumber', '/syarat-ketentuan', '/kebijakan-privasi'],
            'surat' => Surat::orderBy('nomor')->pluck('nomor'),
            'doa' => Doa::orderBy('id')->pluck('id'),
            'hadist' => Hadist::query()->select('id', 'kitab', 'nomor')->lazyById(5000),
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
