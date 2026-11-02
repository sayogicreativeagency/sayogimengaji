# Roadmap Sayogi Mengaji

Project pribadi (Sayogi Creative Agency), bukan pekerjaan Akmanda Media. Tidak dilaporkan ke Harianku.

## Sudah beres

- [x] Pencarian hadist berbantuan AI (DeepSeek) dengan ringkasan, filter derajat/kitab tanpa reload, halaman detail
- [x] Admin Filament: koreksi data hadist, laporan pengunjung, akun dan peran (superadmin, editor, pengguna)
- [x] Al-Qur'an dengan murottal per ayat (6 qori, ulang, kecepatan, lanjut dari terakhir)
- [x] Widget jam sholat dan cuaca
- [x] Infaq lewat QRIS dan Saweria, halaman Dukung
- [x] Syarat & Ketentuan, Kebijakan Privasi, Sumber & Atribusi
- [x] Belajar Al-Qur'an per kata: sorot kata, ketuk untuk dengar, mode hafalan, progres per akun
- [x] Pengaturan qori belajar di admin (Husary Muallim dimatikan sampai ada izin)
- [x] Pembayaran langganan belajar: QRIS bernominal + transfer, konfirmasi bayar, persetujuan admin, saklar gratis/berbayar (2 Nov 2026)
- [x] Widget: minta izin lokasi di kunjungan pertama sampai pengunjung menjawab (2 Nov 2026)
- [x] Widget: tanggal Hijriah (2 Nov 2026)
- [x] Halaman kalender Masehi + Hijriah, ditautkan dari footer dan widget (2 Nov 2026)
- [x] Rebranding jadi Sayogi Mengaji (2 Nov 2026)
- [x] Halaman Tentang kami (Sayogi Creative Agency) di footer (2 Nov 2026)

## Menunggu pihak lain

- [ ] Izin tertulis Quran Foundation untuk fitur belajar berbayar (email terkirim 2 Nov 2026). Sampai ada jawaban, pertimbangkan saklar langganan dimatikan.
- [ ] Isi `BAYAR_REKENING` dan tes pindai QRIS bernominal dari HP
- [ ] Kebijakan pengembalian dana untuk halaman langganan (Syarat & Ketentuan bagian 6 menjanjikannya)

## Berikutnya

### Sugesti pencarian dari kata yang paling sering dicari

Chip contoh di beranda (sekarang daftar tetap di `beranda.blade.php`) diganti kata/frasa yang paling sering dicari 30 hari terakhir, dirapikan LLM sekali sehari.

1. **Catat pencarian.** Tabel `pencarian_harian` (`tanggal`, `kata` ternormalisasi: huruf kecil + spasi dirapikan, `jumlah`, `jumlah_tanpa_hasil`), unik per (`tanggal`, `kata`), di-upsert tiap `/cari`. Tanpa user_id dan IP, jadi tidak menyimpan siapa mencari apa.
2. **Rapikan tiap hari.** Command terjadwal mengambil ±50 kata teratas 30 hari terakhir (minimal dicari beberapa kali, supaya pencarian pribadi satu orang tidak tampil), lalu meminta LLM (laravel/ai, DeepSeek yang sudah ada) menggabungkan yang semakna atau salah ketik dan membuang yang tidak pantas tampil. Hasilnya ±8 sugesti disimpan di `pengaturan` (`sugesti_cari`). Kalau LLM gagal, pakai kata teratas mentah. Kalau data belum cukup, pakai daftar tetap yang sekarang.
3. **Beranda** menampilkan `sugesti_cari`.
4. **Riset di admin.** Halaman Filament "Riset pencarian": kata teratas 30 hari dengan jumlahnya, dan kata yang sering **tanpa hasil** (petunjuk konten atau sinonim yang kurang). Superadmin bisa menyembunyikan satu kata dari sugesti.
5. Tambah satu kalimat di Kebijakan Privasi tentang pencatatan kata kunci tanpa identitas.

Butuh scheduler (`php artisan schedule:run` lewat cron) di server produksi.

Sengaja belum: autocomplete saat mengetik, grafik tren, riwayat pencarian per akun. Layak ditambah kalau data pencarian sudah cukup banyak dan sugesti harian terbukti dipakai.
