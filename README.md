<p align="center"><img src="public/images/logo.webp" width="96" alt="Logo Sayogi Mengaji"></p>

<h1 align="center">Sayogi Mengaji</h1>

<p align="center">Situs untuk mengenal hadist dan Al-Qur'an: cari hadist dengan bahasa sehari-hari, dengarkan murottal, dan hafalkan Al-Qur'an kata demi kata.<br>Dibuat oleh Sayogi Creative Agency.</p>

---

## Fitur

- **Pencarian hadist berbantuan AI.** 36 ribu lebih hadist dari 7 kitab (Kutubus Sittah dan Muwaththa'), lengkap dengan teks Arab, terjemahan, dan derajat. AI (DeepSeek lewat Laravel AI SDK) mengembangkan kata kunci dan menulis ringkasan; filter derajat dan kitab tanpa reload. Halaman `/hadist` menjelaskan arti tiap derajat dan cara situs menentukannya. Sugesti pencarian diambil dari kata yang paling sering dicari, dirapikan AI sekali sehari.
- **Al-Qur'an dan murottal.** Teks, transliterasi, terjemahan Kemenag, dan pemutar murottal per ayat dengan 6 qori (ulang, kecepatan, lanjut dari terakhir).
- **Belajar Al-Qur'an per kata.** Sorot kata mengikuti bacaan, ketuk kata untuk mendengar pelafalannya, mode hafalan (huruf awal / teks tersembunyi), progres tersimpan per akun. Al-Fatihah gratis, surat lain lewat langganan.
- **Doa harian.** Doa dan dzikir sehari-hari per grup dengan teks Arab, latin, arti, dan rujukan; gratis untuk semua.
- **Langganan.** Bayar lewat QRIS (nominal terisi otomatis dengan kode unik) atau transfer, konfirmasi bayar dengan bukti, disetujui admin. Ada saklar di admin untuk menggratiskan semua surat.
- **Jadwal sholat, cuaca, dan kalender.** Widget jam sholat (parameter Kemenag RI, dihitung di browser), cuaca Open-Meteo, tanggal Hijriah; halaman kalender Masehi + Hijriah dengan libur nasional, cuti bersama, dan hari besar Islam.
- **Infaq** lewat QRIS dan Saweria.
- **Panel admin (Filament).** Koreksi data hadist, laporan pengunjung, akun dan peran, persetujuan pembayaran, pengaturan langganan dan qori, statistik murottal.

## Stack

Laravel 13 (PHP ≥ 8.4.1), Filament 5, Laravel AI SDK, MariaDB, Tailwind CSS 4 + Vite, Pest. Font Instrument Sans, Cormorant Garamond, Amiri, dan Amiri Quran disajikan dari server sendiri.

## Menjalankan di lokal

Butuh PHP 8.4, Composer, Node.js, dan MariaDB/MySQL. Contoh di bawah memakai [Laravel Herd](https://herd.laravel.com) untuk PHP dan web server.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

Lalu buat database `hadistapa` (utf8mb4) dan arahkan situsnya:

```bash
herd link hadistapa
herd secure hadistapa
```

`herd secure` penting: izin lokasi browser hanya berjalan di HTTPS, tanpa itu widget sholat selalu memakai Jakarta. Situs terbuka di `https://hadistapa.test`.

### Mengisi data

```bash
php artisan hadist:impor                # semua kitab, atau mis. hadist:impor bukhari
php artisan quran:impor                 # 114 surat dari equran.id, atau quran:impor 36
php artisan quran:impor-kata            # kata Utsmani + waktu per kata (fitur belajar)
php artisan doa:impor                   # doa dan dzikir dari equran.id
php artisan akun:peran email@contoh.com superadmin
```

`akun:langganan email@contoh.com --bulan=12` memberi atau memperpanjang langganan secara manual.

Sugesti pencarian di beranda disusun dari kata yang paling sering dicari 30 hari terakhir oleh `php artisan pencarian:sugesti`, yang dijadwalkan tiap pukul 02.00. Di server, jalankan scheduler lewat cron: `* * * * * php artisan schedule:run`. Hasilnya bisa dipantau dan diatur di admin, menu Riset pencarian.

### Konfigurasi `.env`

| Variabel | Untuk |
|---|---|
| `DB_*` | MariaDB (`DB_CONNECTION=mariadb`) |
| `AI_PROVIDER`, `DEEPSEEK_API_KEY`, `DEEPSEEK_MODEL`, `DEEPSEEK_REASONING_EFFORT` | Pencarian hadist berbantuan AI |
| `INFAQ_QRIS` | Teks QRIS (diawali `000201`) untuk infaq; juga dipakai, dengan nominal terisi, untuk pembayaran langganan |
| `SAWERIA_URL` | Link profil Saweria |
| `BAYAR_REKENING` | Rekening transfer langganan, mis. `BCA 1234567890 a.n. Nama` |

Gambar hero, logo, dan ikon sudah dibuat sebelumnya (OpenAI) dan disimpan di `public/images`; situs tidak memanggil OpenAI saat berjalan.

## Tes

```bash
php artisan test        # Pest, butuh database MariaDB `hadistapa_test` (FULLTEXT tidak didukung SQLite)
npm run test:js         # hitungan jadwal sholat
```

## Sumber data dan lisensi konten

Rinciannya ada di halaman `/sumber`. Hadist dari [fawazahmed0/hadith-api](https://github.com/fawazahmed0/hadith-api) (Unlicense), teks dan murottal Al-Qur'an dari [equran.id](https://equran.id), kata per kata dan waktu per kata dari [Quran.com](https://quran.com) (Quran Foundation).

Catatan untuk fitur berbayar: syarat Quran.com melarang pemakaian kontennya untuk tujuan komersial tanpa izin tertulis, dan audio Husary (Muallim) dari QuranicAudio khusus non-komersial (qori ini dinonaktifkan di admin). Izin Quran Foundation sedang dimintakan; sampai ada jawaban, langganan bisa dimatikan lewat saklar di admin.

## Dokumen

- [docs/ROADMAP.md](docs/ROADMAP.md): yang sudah beres, yang menunggu keputusan, rencana berikutnya, dan catatan produksi (deploy).
- [docs/superpowers/specs](docs/superpowers/specs) dan [docs/plans](docs/plans): desain dan rencana awal (masih memakai nama lama, Hadist Apa).
