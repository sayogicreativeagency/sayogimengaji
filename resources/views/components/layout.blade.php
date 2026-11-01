@props(['judul' => null, 'sidebar' => true])
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#0b1512">
    <title>{{ $judul ? $judul.' — ' : '' }}{{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-800 antialiased dark:bg-stone-950 dark:text-stone-200">
    <header class="pola-islami border-b border-stone-200 dark:border-stone-800">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-6 gap-y-2 px-4 py-4">
            <a href="/" class="font-judul text-2xl font-semibold whitespace-nowrap text-emerald-800 dark:text-emas-300">Hadist Apa</a>
            <nav class="-mx-1 flex max-w-full items-center gap-4 overflow-x-auto px-1 text-sm whitespace-nowrap">
                <a href="/hadist" class="font-medium text-stone-700 hover:text-emerald-800 dark:text-stone-300 dark:hover:text-emas-300">Hadist</a>
                <a href="/quran" class="font-medium text-stone-700 hover:text-emerald-800 dark:text-stone-300 dark:hover:text-emas-300">Al-Qur'an</a>
                <a href="/dukung" class="rounded-full border border-emerald-700/30 px-3 py-1 text-sm font-medium text-emerald-800 hover:bg-emerald-50 dark:border-emas-400/40 dark:text-emas-300 dark:hover:bg-emas-400/10">Dukung</a>
                @auth
                    @if (auth()->user()->peran->bisaMasukAdmin())
                        <a href="/admin" class="font-medium text-stone-700 hover:text-emerald-800 dark:text-stone-300 dark:hover:text-emas-300">Admin</a>
                    @endif
                    <form action="/keluar" method="post" class="flex items-center gap-2">
                        @csrf
                        <span class="hidden text-stone-500 sm:inline">{{ auth()->user()->name }}</span>
                        <button class="font-medium text-stone-700 hover:text-emerald-800 dark:text-stone-300 dark:hover:text-emas-300">Keluar</button>
                    </form>
                @else
                    <a href="/masuk" class="font-medium text-stone-700 hover:text-emerald-800 dark:text-stone-300 dark:hover:text-emas-300">Masuk</a>
                @endauth
            </nav>
        </div>
    </header>

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

    <footer class="mx-auto max-w-6xl px-4 pb-10 text-xs leading-relaxed text-stone-500">
        <div class="ornamen mb-6" aria-hidden="true">✦</div>
        Data hadist: <a href="https://github.com/fawazahmed0/hadith-api" class="underline">fawazahmed0/hadith-api</a>.
        Data &amp; audio Al-Qur'an: <a href="https://equran.id" class="underline">equran.id</a> (terjemahan Kemenag).
        Derajat mengikuti penilaian ulama yang tercantum; rujuk ulama untuk penetapan hukum.
        <a href="/kontak" class="underline">Kontak &amp; laporan</a> · <a href="/dukung" class="underline">Dukung Hadist Apa</a>.
    </footer>
</body>
</html>
