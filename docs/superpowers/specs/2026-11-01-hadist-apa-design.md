# Hadist Apa — Desain v1

Tanggal: 2026-11-01
Status: disetujui per bagian, menunggu review spec

## Tujuan

Web publik untuk mencari hadist dengan bahasa sehari-hari ("hadist tentang pernikahan").
Hasil: ringkasan singkat buatan AI + daftar hadist dari DB lengkap dengan teks Arab,
terjemahan Indonesia, rujukan kitab/nomor, dan derajat (shahih s/d maudhu).
Admin bisa mengoreksi data.

## Stack

- Laravel 13, PHP 8.4 (Herd, `http://hadistapa.test`), Pest 5.
- DB: MariaDB 10.4 lokal (XAMPP), database `hadistapa`; test pakai `hadistapa_test`.
- Frontend: Blade + Tailwind 4 (Vite), tanpa framework JS. Font Arab: Amiri.
- Admin: Filament 5.
- AI: `laravel/ai`. Default DeepSeek V4.1 Flash; provider/model dari `.env`
  sehingga bisa pindah ke OpenRouter/OpenAI/Anthropic tanpa ubah kode.

## Sumber data

`fawazahmed0/hadith-api` via jsDelivr
(`https://cdn.jsdelivr.net/gh/fawazahmed0/hadith-api@1/editions/{edisi}.json`).

7 kitab yang punya edisi Indonesia (`ind-*`) dan Arab (`ara-*`), total ±36.390 hadist:

| kode | nama | jumlah |
|---|---|---|
| bukhari | Shahih Bukhari | 7.589 |
| muslim | Shahih Muslim | 7.563 |
| abudawud | Sunan Abu Dawud | 5.274 |
| tirmidhi | Sunan Tirmidzi | 3.998 |
| nasai | Sunan Nasa'i | 5.765 |
| ibnmajah | Sunan Ibnu Majah | 4.343 |
| malik | Muwaththa' Malik | 1.858 |

Tiap hadist di dataset: `hadithnumber`, `arabicnumber`, `text`, `grades[{name, grade}]`,
`reference{book, hadith}`. Judul bab ada di `metadata.section` (bahasa Inggris + nama Arab,
mis. "Marriage (Kitab Al-Nikah)").

## Data

### Enum `App\Enums\Kitab`

Backed string enum berisi 7 kode di atas, dengan `label()` untuk nama tampilan.
Tidak ada tabel kitab.

### Tabel `hadist`

| kolom | tipe | catatan |
|---|---|---|
| id | bigint PK | |
| kitab | string(20) | nilai enum `Kitab` |
| nomor | unsigned int | `hadithnumber` |
| nomor_arab | string(20) nullable | `arabicnumber` (bisa desimal di dataset) |
| bab_nomor | unsigned int nullable | `reference.book` |
| bab_judul | string nullable | dari `metadata.section` |
| teks_arab | longText nullable | dari edisi `ara-*` |
| terjemahan | text | dari edisi `ind-*` |
| derajat | json | `[{ulama, derajat}]`, apa adanya dari dataset |
| derajat_ringkas | string(10) nullable | `shahih` / `hasan` / `dhaif` / `maudhu` / null |
| dikoreksi_at | timestamp nullable | diisi saat admin menyimpan |
| timestamps | | |

Index: unique `(kitab, nomor)`, FULLTEXT `(terjemahan, bab_judul)`, index `derajat_ringkas`.

### Aturan `derajat_ringkas`

1. Kitab `bukhari` / `muslim` → `shahih` (keterangan tampilan: "kitab shahih").
2. Selain itu: ambil penilaian Al-Albani bila ada, jika tidak ambil yang pertama.
3. Cocokkan teks (case-insensitive, urutan dicek dari atas):
   `sahih` → shahih (termasuk "Hasan Sahih"), `hasan` → hasan,
   `da'if` / `daif` / `dhaif` → dhaif, `maudu` / `mawdu` → maudhu.
4. Tidak cocok / tidak ada penilaian → null ("belum dinilai").

## Impor

`php artisan hadist:impor {kitab?}` (tanpa argumen = semua kitab).

1. Ambil `ind-{kitab}.json` dan `ara-{kitab}.json`.
2. Gabung berdasarkan `hadithnumber`; `bab_judul` dari `metadata.section[reference.book]`.
3. Upsert per batch 500 berdasarkan `(kitab, nomor)`.
4. Baris dengan `dikoreksi_at` terisi dilewati (koreksi admin tidak ditimpa).
5. Gagal unduh satu kitab → laporkan error dan lanjut ke kitab berikutnya; exit code non-zero
   bila ada yang gagal.

## Pencarian

`GET /cari?q=&derajat=&kitab=` → satu kelas aksi `App\Actions\CariHadist`.

1. **Cache**: `Cache::remember` 7 hari, kunci = hash dari query ternormalisasi
   (lowercase, trim, spasi tunggal) + filter.
2. **AI 1 — pengembangan kata kunci** (output terstruktur): 5–12 kata kunci Indonesia +
   istilah Arab yang ditulis latin.
3. **DB**: `MATCH(terjemahan, bab_judul) AGAINST(? IN BOOLEAN MODE)` + filter, 30 kandidat
   teratas menurut skor.
4. **AI 2 — pilih ulang + ringkas** (output terstruktur): input 30 kandidat (`id` +
   terjemahan dipotong ±600 karakter + derajat). Output
   `{ringkasan: string, hadist_ids: int[] (≤10, urut relevansi)}`.
   Ringkasan 2–4 paragraf, rujukan berformat `[#id]`.
5. **Render**: ringkasan + kartu hadist yang diambil dari DB berdasarkan `hadist_ids`.

### Pencegahan halusinasi

- AI tidak pernah menghasilkan teks hadist; semua teks hadist yang tampil berasal dari DB.
- `hadist_ids` di luar 30 kandidat dibuang; rujukan `[#id]` yang tidak valid dihapus dari ringkasan.
- Instruksi ke AI: jangan berfatwa, sebutkan derajat, katakan jujur bila tidak ada yang relevan.
- Disclaimer tetap: "Ringkasan dibuat AI, rujuk ulama untuk hukum."

### Fallback

Bila panggilan AI melempar exception (timeout, kuota, key kosong):
- Gagal di langkah 2 → FULLTEXT memakai kata dari query mentah.
- Gagal di langkah 4 → urutan skor FULLTEXT, 10 teratas, tanpa ringkasan +
  catatan "ringkasan AI sedang tidak tersedia".
- Hasil fallback tidak di-cache.

Query kosong → redirect ke `/`. Tidak ada kandidat → pesan "tidak ditemukan" tanpa memanggil AI 2.

### Biaya perkiraan

±4k token input + 600 output per pencarian tanpa cache ≈ $0,002 (DeepSeek Flash, harga Okt 2026).

## Halaman publik

- `/` — kotak pencarian + contoh query yang bisa diklik.
- `/cari` — ringkasan AI (rujukan `[#id]` jadi link ke kartu), kartu hadist (Arab RTL,
  terjemahan, "HR. {kitab} no. {nomor}", label derajat berwarna: hijau shahih, biru hasan,
  kuning dha'if, merah maudhu, abu-abu belum dinilai), filter derajat & kitab via form GET.
- `/hadist/{kitab}/{nomor}` — detail: semua penilaian ulama, judul bab. 404 bila tidak ada.

## Admin

Filament 5 di `/admin`, login dengan tabel `users` bawaan; akun lewat
`php artisan make:filament-user`.

`HadistResource`: tabel dengan pencarian + filter kitab/derajat; form edit `terjemahan`,
`derajat_ringkas`, `bab_judul`. Menyimpan mengisi `dikoreksi_at`. Tanpa create/delete.

## Konfigurasi `.env`

```
AI_PROVIDER=deepseek
AI_MODEL=deepseek-chat        # sesuaikan nama model Flash saat implementasi
DEEPSEEK_API_KEY=
```

## Testing (Pest)

- Unit: pemetaan `derajat_ringkas` (Hasan Sahih, Da'if, tanpa grade, Bukhari, tidak dikenal).
- Impor dengan `Http::fake` + fixture JSON kecil: penggabungan ind/ara, upsert idempoten,
  baris `dikoreksi_at` tidak tertimpa.
- Pencarian dengan fake `laravel/ai`: id di luar kandidat dibuang, fallback saat AI exception,
  cache mencegah panggilan AI kedua, tanpa kandidat tidak memanggil AI 2.
- Test DB memakai MariaDB `hadistapa_test` (FULLTEXT tidak didukung SQLite).

## Di luar v1

Login pengguna, bookmark, chat lanjutan, embedding/vektor (butuh MariaDB ≥11.7),
tag topik AI, create/delete hadist manual. Skema cukup ditambah kolom/tabel untuk itu nanti.
