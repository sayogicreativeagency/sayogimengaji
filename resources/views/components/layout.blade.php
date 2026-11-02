{{-- penuh: halaman mengatur lebar sendiri (beranda), tanpa kontainer dan sidebar. --}}
@props(['judul' => null, 'sidebar' => true, 'penuh' => false])
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#0b1512">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judul ? $judul.' — ' : '' }}{{ config('app.name') }}</title>
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

    <footer class="mx-auto max-w-6xl px-4 pb-10 text-xs leading-relaxed text-stone-500">
        <div class="ornamen mb-6" aria-hidden="true">✦</div>
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
