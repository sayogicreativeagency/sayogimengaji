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
- [x] Kalender: libur nasional + cuti bersama (Google) dan hari besar Islam di bawah kalender, Jumat hijau (2 Nov 2026)
- [x] Beranda gaya company profile: hero layar penuh (gambar OpenAI), widget melayang, section Bantu di atas footer (2 Nov 2026)
- [x] Ikon cuaca dan pin lokasi 3D (OpenAI) menggantikan emoji (2 Nov 2026)
- [x] Logo emblem + tagline Imam asy-Syafi'i, favicon, menu burger HP dengan panel geser (2 Nov 2026)
- [x] Font Amiri benar-benar dipakai untuk huruf Arab (sebelumnya hanya subset Latin yang dimuat) (2 Nov 2026)
- [x] Halaman `/hadist` sendiri: form cari, penjelasan derajat, daftar 7 kitab dengan jumlah hadist (2 Nov 2026)
- [x] Sugesti pencarian dari kata terbanyak dicari 30 hari terakhir (dirapikan LLM tiap 02.00, `pencarian:sugesti`), halaman Riset pencarian di admin (2 Nov 2026)
- [x] SEO: judul dan deskripsi per halaman (surat, hadist dari matannya, kalender per bulan), canonical, Open Graph, JSON-LD situs, noindex untuk hasil cari dan akun, `sitemap.xml` (±36.500 URL) dan `robots.txt` dinamis (2 Nov 2026)

- [x] Beranda: CTA Baca Al-Qur'an, section baca & hafal dengan pencarian surat dan mini player murottal, cari hadist jadi section ketiga (4 Nov 2026)
- [x] Footer: tombol bagikan halaman ke WhatsApp dengan kutipan "Sampaikanlah dariku walau hanya satu ayat" (HR. Bukhari 3461) (4 Nov 2026)
- [x] Statistik murottal di admin (superadmin): diputar, tamat, ayat berhenti, IP, waktu dengar, qori, perangkat; 24 jam, hari ini, 7 hari; data terhapus setelah 90 hari, tertulis di Kebijakan Privasi (4 Nov 2026)
- [x] Halaman Doa: doa harian dari equran.id per grup, cari di tempat, halaman per doa dengan rujukan; gratis (5 Nov 2026)
- [x] Beranda: section cari doa di bawah cari hadist, 12 sugesti doa umum pilihan redaksi (5 Nov 2026)

## Perlu keputusan Angga

- [x] Syarat & Ketentuan: pengelola Sayogi Creative Agency, bagian 6 mengatur langganan (harga, aktivasi, tanpa perpanjangan otomatis, refund hanya bila ada kesalahan) (2 Nov 2026)
- [x] Kalimat "gratis" di beranda, kartu donasi, dan halaman Dukung dibatasi ke fitur yang memang gratis (2 Nov 2026)
- [ ] Baca ulang penjelasan derajat dan tahun wafat para imam di `/hadist` sebelum tayang.

## Menunggu pihak lain

- [ ] Izin tertulis Quran Foundation untuk fitur belajar berbayar (email terkirim 2 Nov 2026). Sampai ada jawaban, pertimbangkan saklar langganan dimatikan.
- [ ] Isi `BAYAR_REKENING` dan tes pindai QRIS bernominal dari HP

## Berikutnya

Belum ada item besar. Yang sengaja ditunda dari sugesti pencarian: autocomplete saat mengetik, grafik tren, riwayat pencarian per akun; layak ditambah kalau data pencarian sudah cukup banyak dan sugesti harian terbukti dipakai.

## Produksi

Situs utama: https://mengaji.sayogicreative.com di server NEO Biznet Gio (Nginx, PHP 8.4, MariaDB lokal, HTTPS Certbot). Render dan Aiven sudah dipensiunkan (4 Nov 2026).

Deploy: push ke GitHub, lalu di server `sudo deploy-laravel mengaji.sayogicreative.com https://github.com/sayogicreativeagency/sayogimengaji.git mengaji`. Script itu menjalankan composer, build Vite, migrate, cache, dan cron `schedule:run`.

- [ ] Isi `BAYAR_REKENING` di `.env` server (opsi transfer belum tampil).
- [ ] Daftarkan domain di Google Search Console lalu kirim `https://mengaji.sayogicreative.com/sitemap.xml`.
