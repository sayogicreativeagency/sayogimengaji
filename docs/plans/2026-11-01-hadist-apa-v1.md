# Rencana Implementasi Hadist Apa v1

Spec: [2026-11-01-hadist-apa-design.md](../superpowers/specs/2026-11-01-hadist-apa-design.md)

## Modul

Tiga modul dalam yang memegang hampir semua perilaku. Controller, command, dan
Filament hanya jadi pemanggil tipis.

### 1. `Derajat` — enum + pemetaan (in-process)

```php
enum Derajat: string { case Shahih = 'shahih'; case Hasan = 'hasan'; case Dhaif = 'dhaif'; case Maudhu = 'maudhu';
    public static function dariPenilaian(Kitab $kitab, array $grades): ?self; // aturan spec
    public function label(): string; public function warna(): string;
}
```

Interface: satu fungsi murni. Semua aturan (Bukhari/Muslim, prioritas Al-Albani,
pencocokan teks) tersembunyi di dalamnya. Dites langsung, tanpa seam.

### 2. `ImporHadist` — action (lokal-substitusi: `Http::fake`)

```php
final class ImporHadist { public function __invoke(Kitab $kitab): int; } // jumlah baris di-upsert
```

Interface: satu kitab masuk, angka keluar. Melempar `RequestException` bila unduhan gagal.
Di dalamnya: unduh ind+ara, gabung per nomor, judul bab, `Derajat::dariPenilaian`, upsert
batch 500, lewati `dikoreksi_at`. Command `hadist:impor` cuma loop kitab + try/catch + output.

Seam untuk HTTP: facade `Http` milik Laravel (adapter produksi + `Http::fake`). Tidak perlu port sendiri.

### 3. `CariHadist` — action (eksternal: AI)

```php
final class CariHadist { public function __invoke(string $q, ?Derajat $derajat = null, ?Kitab $kitab = null): HasilPencarian; }
final readonly class HasilPencarian { public function __construct(
    public ?string $ringkasan,          // null = tanpa ringkasan (fallback/tidak ada hasil)
    public Collection $hadist,          // Hadist models, urut relevansi, ≤10
    public bool $aiTersedia,
) {} }
```

Interface: query + filter masuk, hasil siap tampil keluar. Cache, kedua panggilan AI,
FULLTEXT, validasi id, pembersihan `[#id]`, dan fallback semuanya di dalam.

Seam AI: dua agent class `laravel/ai`, yaitu `PengembangKataKunci` dan `PemilihHadist`.
Adapter produksinya provider sungguhan, adapter test-nya `::fake()` bawaan SDK.
Tidak perlu port sendiri. FULLTEXT ditaruh sebagai scope `Hadist::scopeCocok($q, $kataKunci)`,
dipakai hanya oleh `CariHadist` (seam internal).

Test lewat interface `CariHadist` + DB MariaDB test + agent fake.

## Risiko yang dicek pertama (spike, dibuang setelahnya)

`laravel/ai` tidak mendokumentasikan bahwa DeepSeek mendukung structured output, dan tidak
menunjukkan cara membuat fake melempar exception. Sebelum menulis `CariHadist`:

1. Pasang `laravel/ai`, isi `DEEPSEEK_API_KEY`, panggil agent dengan `schema()` ke DeepSeek.
   - Jalan → lanjut.
   - Tidak → pakai driver `openai-compatible` dengan URL DeepSeek (structured output
     terdokumentasi untuk driver itu). Kalau tetap gagal, minta JSON lewat instruksi lalu
     `json_decode` + validasi.
2. Pastikan nama model Flash yang benar untuk `.env`.
3. Cek apakah closure di `Agent::fake(fn () => throw ...)` meneruskan exception. Kalau tidak,
   fallback dites dengan meng-bind agent palsu ke container.

## Urutan kerja

Setiap langkah: test dulu (merah), implementasi, hijau, `vendor/bin/pint`.

1. **Git + DB test**: `git init`, commit awal. Buat DB `hadistapa_test`, set `phpunit.xml`
   (`DB_CONNECTION=mariadb`, `DB_DATABASE=hadistapa_test`), test pakai `RefreshDatabase`.
2. **Skema**: enum `Kitab`, migration `hadist` (index unik, FULLTEXT, `derajat_ringkas`),
   model `Hadist` (cast `derajat` array, `derajat_ringkas` → `Derajat`, `kitab` → `Kitab`), factory.
3. **`Derajat::dariPenilaian`**: test tabel kasus dari spec → implementasi.
4. **`ImporHadist` + command**: fixture JSON kecil di `tests/Fixtures/`, test penggabungan,
   idempoten, `dikoreksi_at`. Lalu jalankan impor sungguhan dan cek jumlah per kitab ≈ spec.
5. **Spike AI** (bagian risiko di atas). Hasilnya dicatat di spec bila ada yang berubah.
6. **`CariHadist`**: agent `PengembangKataKunci`, `PemilihHadist`, scope `cocok`, DTO.
   Test: jalur normal, id di luar kandidat dibuang, `[#id]` tidak valid dihapus, fallback
   AI-1 gagal, fallback AI-2 gagal (tanpa cache), cache mencegah panggilan kedua, tanpa
   kandidat → AI-2 tidak dipanggil.
7. **Halaman publik**: `HadistController` (`beranda`, `cari`, `tampil`), view Blade + Tailwind,
   font Amiri, label derajat, link `[#id]`. Feature test: query kosong redirect, 404 detail,
   hasil tampil dengan agent fake.
8. **Admin**: `composer require filament/filament`, panel `/admin`, `HadistResource`
   (tabel + filter + edit, `dikoreksi_at` diisi saat simpan, tanpa create/delete). Test:
   simpan mengisi `dikoreksi_at`.
9. **Verifikasi manual**: buka `hadistapa.test`, coba "hadist tentang pernikahan",
   "keutamaan sedekah", filter dha'if, buka detail, edit satu hadist di admin, impor ulang
   kitab itu → koreksi tetap.

## Yang dibutuhkan dari user

- `DEEPSEEK_API_KEY` sebelum langkah 5.
- Persetujuan `git init` di langkah 1.
