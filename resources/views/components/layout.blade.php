@props(['judul' => null, 'sidebar' => true])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $judul ? $judul.' — ' : '' }}{{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-800 antialiased dark:bg-stone-950 dark:text-stone-200">
    <header class="border-b border-stone-200 dark:border-stone-800">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="/" class="text-lg font-semibold tracking-tight text-emerald-800 dark:text-emerald-400">Hadist Apa</a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="/hadist" class="font-medium text-stone-700 hover:text-emerald-800 dark:text-stone-300 dark:hover:text-emerald-400">Hadist</a>
                <a href="/quran" class="font-medium text-stone-700 hover:text-emerald-800 dark:text-stone-300 dark:hover:text-emerald-400">Al-Qur'an</a>
                <a href="/dukung" class="rounded-full border border-emerald-700/30 px-3 py-1 text-sm font-medium text-emerald-800 hover:bg-emerald-50 dark:border-emerald-400/30 dark:text-emerald-400 dark:hover:bg-emerald-950/40">Dukung</a>
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
        Data hadist: <a href="https://github.com/fawazahmed0/hadith-api" class="underline">fawazahmed0/hadith-api</a>.
        Data &amp; audio Al-Qur'an: <a href="https://equran.id" class="underline">equran.id</a> (terjemahan Kemenag).
        Derajat mengikuti penilaian ulama yang tercantum; rujuk ulama untuk penetapan hukum.
        <a href="/kontak" class="underline">Kontak &amp; laporan</a> · <a href="/dukung" class="underline">Dukung Hadist Apa</a>.
    </footer>
</body>
</html>
