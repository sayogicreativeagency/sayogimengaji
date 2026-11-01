@props(['judul' => null])
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
        <div class="mx-auto flex max-w-3xl items-center justify-between px-4 py-4">
            <a href="/" class="text-lg font-semibold tracking-tight text-emerald-800 dark:text-emerald-400">Hadist Apa</a>
            <span class="text-xs text-stone-500">Kumpulan hadist 7 kitab</span>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="mx-auto max-w-3xl px-4 pb-10 text-xs leading-relaxed text-stone-500">
        Data: <a href="https://github.com/fawazahmed0/hadith-api" class="underline">fawazahmed0/hadith-api</a>.
        Derajat mengikuti penilaian ulama yang tercantum; rujuk ulama untuk penetapan hukum.
    </footer>
</body>
</html>
