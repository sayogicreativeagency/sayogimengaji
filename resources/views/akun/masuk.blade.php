<x-layout judul="Masuk" :sidebar="false" :indeks="false">
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-semibold tracking-tight">Masuk</h1>
        <p class="mt-2 text-stone-600 dark:text-stone-400">Belum punya akun? <a href="/daftar" class="font-medium text-emerald-700 hover:underline dark:text-emas-400">Daftar</a></p>

        <form action="/masuk" method="post" class="mt-6 space-y-4 rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-800 dark:bg-stone-900">
            @csrf
            <x-kolom nama="email" label="Email" type="email" required autocomplete="email" autofocus />
            <x-kolom nama="password" label="Password" type="password" required autocomplete="current-password" />
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="ingat" value="1" class="accent-emerald-700"> Ingat saya</label>
            <button class="w-full rounded-lg bg-emerald-700 px-5 py-2.5 font-medium text-white hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 focus-visible:outline-none">Masuk</button>
        </form>
    </div>
</x-layout>
