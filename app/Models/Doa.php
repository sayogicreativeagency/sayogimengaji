<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doa extends Model
{
    /**
     * Doa yang paling umum dipakai, pilihan redaksi: label sugesti di beranda => nama doa dari sumber dan kata yang
     * biasa diketik orang tapi tidak ada di teksnya. Dicocokkan lewat nama supaya tetap benar kalau sumber mengubah id.
     *
     * @var array<string, array{nama: string, kata: string}>
     */
    public const SUGESTI = [
        'Menjenguk orang sakit' => ['nama' => 'Doa Kepada Orang Sakit', 'kata' => 'menjenguk jenguk besuk'],
        'Melayat' => ['nama' => "Doa Belasungkawa (Ta'ziyah)", 'kata' => 'melayat layat takziah'],
        'Bepergian' => ['nama' => 'Doa Bepergian', 'kata' => 'pergi safar mudik'],
        'Keluar rumah' => ['nama' => 'Doa Keluar Rumah', 'kata' => 'pergi berangkat'],
        'Naik kendaraan' => ['nama' => 'Doa Naik Kendaraan', 'kata' => 'mobil motor'],
        'Sebelum makan' => ['nama' => 'Doa Sebelum Makan', 'kata' => ''],
        'Setelah makan' => ['nama' => 'Doa Setelah Makan', 'kata' => 'sesudah'],
        'Sebelum tidur' => ['nama' => 'Doa Sebelum Tidur 1', 'kata' => ''],
        'Bangun tidur' => ['nama' => 'Doa Bangun Tidur 1', 'kata' => ''],
        'Masuk kamar mandi' => ['nama' => 'Doa Masuk Kamar Mandi', 'kata' => 'toilet wc'],
        'Ziarah kubur' => ['nama' => 'Doa Ziarah Kubur', 'kata' => 'nyekar'],
        'Saat sedih' => ['nama' => 'Doa Ketika Sedih, Gundah Atau Galau', 'kata' => ''],
    ];

    protected $table = 'doa';

    public $incrementing = false;

    protected $guarded = [];

    /** Teks yang dicocokkan kotak cari di halaman doa. */
    public function teksCari(): string
    {
        $kata = collect(self::SUGESTI)->firstWhere('nama', $this->nama)['kata'] ?? '';

        return mb_strtolower("{$this->grup} {$this->nama} {$this->arti} {$kata}");
    }

    /** Keterangan dari sumber berupa teks biasa: di-escape dulu, lalu alamat rujukannya dijadikan tautan. */
    public function tentangHtml(): string
    {
        return preg_replace('~https?://[^\s<]+~', '<a href="$0" rel="nofollow noopener" target="_blank">$0</a>', e($this->tentang));
    }
}
