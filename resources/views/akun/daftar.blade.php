<x-layout judul="Daftar" :sidebar="false">
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-semibold tracking-tight">Daftar akun</h1>
        <p class="mt-2 text-stone-600 dark:text-stone-400">Sudah punya akun? <a href="/masuk" class="font-medium text-emerald-700 hover:underline dark:text-emas-400">Masuk</a></p>

        <form action="/daftar" method="post" class="mt-6 space-y-4 rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-800 dark:bg-stone-900">
            @csrf
            <x-kolom nama="name" label="Nama" required maxlength="100" autocomplete="name" autofocus />
            <x-kolom nama="email" label="Email" type="email" required autocomplete="email" />
            <x-kolom nama="password" label="Password" type="password" required minlength="8" autocomplete="new-password" />
            <x-kolom nama="password_confirmation" label="Ulangi password" type="password" required minlength="8" autocomplete="new-password" />
            <p class="text-xs leading-relaxed text-stone-500">Dengan mendaftar, kamu menyetujui <a href="/syarat-ketentuan" class="underline">Syarat &amp; Ketentuan</a> dan <a href="/kebijakan-privasi" class="underline">Kebijakan Privasi</a>.</p>
            <button class="w-full rounded-lg bg-emerald-700 px-5 py-2.5 font-medium text-white hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 focus-visible:outline-none">Daftar</button>
        </form>
    </div>
</x-layout>
