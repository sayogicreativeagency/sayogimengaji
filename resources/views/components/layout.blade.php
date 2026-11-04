{{-- penuh: halaman mengatur lebar sendiri (beranda), tanpa kontainer dan sidebar.
     deskripsi: meta description + Open Graph; indeks=false: noindex (hasil cari, akun, langganan). --}}
@props(['judul' => null, 'sidebar' => true, 'penuh' => false, 'deskripsi' => null, 'indeks' => true, 'kanonik' => null])
{{-- Satu blok PHP saja di sini: bentuk satu baris di berkas yang juga punya blok (menu di bawah) membuat Blade menelan semua di antaranya. --}}
@php
    $kanonik ??= url()->current();
    $deskripsi ??= 'Cari hadist dengan bahasa sehari-hari, baca Al-Qur\'an dengan murottal, belajar dan menghafal per kata, plus jadwal sholat dan kalender Hijriah.';
@endphp
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#0b1512">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judul ? $judul.' — ' : '' }}{{ config('app.name') }}</title>
    <meta name="description" content="{{ $deskripsi }}">
    @if ($indeks)
        <link rel="canonical" href="{{ $kanonik }}">
    @else
        <meta name="robots" content="noindex, follow">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="{{ $judul ?? config('app.name') }}">
    <meta property="og:description" content="{{ $deskripsi }}">
    <meta property="og:url" content="{{ $kanonik }}">
    <meta property="og:image" content="{{ url('/images/og.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    {{ $head ?? '' }}
    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-800 antialiased dark:bg-stone-950 dark:text-stone-200">
    @php
        $menu = ['/hadist' => 'Hadist', '/belajar' => 'Belajar', '/quran' => "Al-Qur'an"];
        if (auth()->user()?->peran->bisaMasukAdmin()) {
            $menu['/admin'] = 'Admin';
        }
    @endphp
    <header class="border-b border-stone-200 bg-stone-950 dark:border-stone-800">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-4">
            <x-logo />

            <nav aria-label="Menu utama" class="hidden items-center gap-4 text-sm whitespace-nowrap md:flex">
                @foreach ($menu as $url => $label)
                    <a href="{{ $url }}" class="font-medium text-stone-700 hover:text-emerald-800 dark:text-stone-300 dark:hover:text-emas-300">{{ $label }}</a>
                @endforeach
                <a href="/dukung" class="rounded-full border border-emerald-700/30 px-3 py-1 text-sm font-medium text-emerald-800 hover:bg-emerald-50 dark:border-emas-400/40 dark:text-emas-300 dark:hover:bg-emas-400/10">Dukung</a>
                @auth
                    <form action="/keluar" method="post" class="flex items-center gap-2">
                        @csrf
                        <span class="hidden text-stone-500 lg:inline">{{ auth()->user()->name }}</span>
                        <button class="font-medium text-stone-700 hover:text-emerald-800 dark:text-stone-300 dark:hover:text-emas-300">Keluar</button>
                    </form>
                @else
                    <a href="/masuk" class="font-medium text-stone-700 hover:text-emerald-800 dark:text-stone-300 dark:hover:text-emas-300">Masuk</a>
                @endauth
            </nav>

            <button type="button" data-buka-menu aria-haspopup="dialog" aria-controls="menu-hp"
                    class="-mr-2 grid size-11 place-items-center rounded-lg text-stone-200 hover:text-emas-300 focus-visible:ring-2 focus-visible:ring-emas-400 focus-visible:outline-none md:hidden">
                <span class="sr-only">Buka menu</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" class="size-6" aria-hidden="true"><path d="M4 7h16M4 12h16M8 17h12"/></svg>
            </button>
        </div>

        {{-- Menu HP: panel layar penuh yang menggeser masuk dari kanan (.menu-hp di app.css, buka/tutup di app.js). --}}
        <dialog id="menu-hp" aria-label="Menu" class="menu-hp m-0 h-dvh max-h-none w-full max-w-none bg-stone-950 p-0 text-stone-200 md:hidden">
            <div class="flex h-full flex-col px-4 pt-4 pb-8">
                <div class="flex items-center justify-between gap-6">
                    <x-logo />
                    <button type="button" data-tutup-menu
                            class="-mr-2 grid size-11 place-items-center rounded-lg text-stone-200 hover:text-emas-300 focus-visible:ring-2 focus-visible:ring-emas-400 focus-visible:outline-none">
                        <span class="sr-only">Tutup menu</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" class="size-6" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                </div>

                <nav aria-label="Menu utama" class="mt-14 flex flex-col">
                    @foreach ($menu as $url => $label)
                        <a href="{{ $url }}" class="border-b border-stone-800 py-4 font-judul text-4xl font-semibold text-stone-100 hover:text-emas-300">{{ $label }}</a>
                    @endforeach
                </nav>

                <div class="mt-auto space-y-5 pt-10">
                    <a href="/dukung" class="block rounded-full bg-emas-400 px-6 py-3.5 text-center font-medium text-stone-950 hover:bg-emas-300">Dukung Sayogi Mengaji</a>
                    @auth
                        <form action="/keluar" method="post" class="flex items-center justify-between gap-4 text-sm">
                            @csrf
                            <span class="truncate text-stone-400">{{ auth()->user()->name }}</span>
                            <button class="font-medium text-stone-200 hover:text-emas-300">Keluar</button>
                        </form>
                    @else
                        <a href="/masuk" class="block text-center font-medium text-stone-200 hover:text-emas-300">Masuk</a>
                    @endauth
                </div>
            </div>
        </dialog>
    </header>

    @if ($penuh)
        <main>
            {{ $slot }}
        </main>
    @else
        {{-- 70% konten, 30% sidebar di layar lebar; sidebar turun ke bawah konten di HP. --}}
        <div class="mx-auto max-w-6xl px-4 py-8 {{ $sidebar ? 'grid gap-8 lg:grid-cols-[minmax(0,7fr)_minmax(0,3fr)]' : '' }}">
            <main class="min-w-0">
                {{ $slot }}
            </main>

            @if ($sidebar)
                <aside aria-label="Jadwal sholat dan dukungan" class="space-y-6 lg:self-start">
                    <x-widget-sholat />
                    <x-papan-donasi ringkas />
                </aside>
            @endif
        </div>
    @endif

    {{-- Ornamen emas menumpang tepat di batas atas footer, jadi pemisah dengan section terakhir. --}}
    <footer class="relative mx-auto max-w-6xl px-4 pt-10 pb-10 text-xs leading-relaxed text-stone-500">
        <div class="ornamen absolute inset-x-4 top-0 -translate-y-1/2" aria-hidden="true">✦</div>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-x-6 gap-y-3">
            <p class="font-judul text-2xl font-semibold text-stone-100">“Sampaikanlah dariku walau hanya satu ayat.” <a href="/hadist/bukhari/3461" class="font-sans text-xs font-normal text-stone-400 underline hover:text-emas-300">HR. Bukhari no. 3461</a></p>
            <a href="https://wa.me/?text={{ rawurlencode("“Sampaikanlah dariku walau hanya satu ayat.” (HR. Bukhari)\n\n".url()->full()) }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 rounded-full border border-emas-400/40 px-4 py-2.5 text-sm font-medium text-emas-300 hover:bg-emas-400/10 focus-visible:ring-2 focus-visible:ring-emas-400 focus-visible:outline-none">
                <svg viewBox="0 0 24 24" fill="currentColor" class="size-5" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Bagikan ke WhatsApp
            </a>
        </div>
        Derajat hadist mengikuti penilaian ulama yang tercantum; rujuk ulama untuk penetapan hukum.
        <nav class="mt-2 flex flex-wrap gap-x-3 gap-y-1">
            <a href="/tentang" class="underline">Tentang kami</a>
            <a href="/kalender" class="underline">Kalender</a>
            <a href="/syarat-ketentuan" class="underline">Syarat &amp; Ketentuan</a>
            <a href="/kebijakan-privasi" class="underline">Kebijakan Privasi</a>
            <a href="/sumber" class="underline">Sumber &amp; Atribusi</a>
            <a href="/kontak" class="underline">Kontak &amp; laporan</a>
            <a href="/dukung" class="underline">Dukung Sayogi Mengaji</a>
        </nav>
        <p class="mt-2">© {{ date('Y') }} Sayogi Mengaji · dibuat oleh Sayogi Creative Agency</p>
    </footer>
</body>
</html>
