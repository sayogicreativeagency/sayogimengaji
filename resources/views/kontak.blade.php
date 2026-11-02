<x-layout judul="Kontak" deskripsi="Laporkan ringkasan, terjemahan, atau derajat hadist yang keliru, atau kirim saran untuk Sayogi Mengaji.">
    <h1 class="text-2xl font-semibold tracking-tight">Kontak &amp; laporan</h1>
    <p class="mt-2 text-stone-600 dark:text-stone-400">
        Menemukan ringkasan yang tidak sesuai dengan hadistnya, terjemahan yang keliru, atau derajat yang salah?
        Ceritakan di sini. Sebutkan nomor rujukan (mis. [3] atau HR. Muslim no. 1400) supaya mudah kami cek.
    </p>

    @if (session('status'))
        <p role="status" class="mt-6 rounded-lg bg-emerald-50 px-4 py-3 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">{{ session('status') }}</p>
    @endif

    <form action="/kontak" method="post" class="mt-6 space-y-4">
        @csrf
        <div class="hidden" aria-hidden="true">
            <label for="situs">Jangan diisi</label>
            <input id="situs" name="situs" type="text" tabindex="-1" autocomplete="off">
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="nama" class="mb-1 block text-sm font-medium">Nama <span class="font-normal text-stone-500">(opsional)</span></label>
                <input id="nama" name="nama" type="text" maxlength="100" value="{{ old('nama') }}" autocomplete="name"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 dark:border-stone-700 dark:bg-stone-900">
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm font-medium">Email <span class="font-normal text-stone-500">(opsional, kalau ingin dibalas)</span></label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 dark:border-stone-700 dark:bg-stone-900">
                @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="q" class="mb-1 block text-sm font-medium">Pencarian terkait <span class="font-normal text-stone-500">(opsional)</span></label>
            <input id="q" name="q" type="text" maxlength="200" value="{{ old('q', $q) }}"
                   class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 dark:border-stone-700 dark:bg-stone-900">
        </div>

        <div>
            <label for="pesan" class="mb-1 block text-sm font-medium">Pesan</label>
            <textarea id="pesan" name="pesan" rows="6" required minlength="5" maxlength="5000"
                      class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 dark:border-stone-700 dark:bg-stone-900">{{ old('pesan') }}</textarea>
            @error('pesan') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <button class="rounded-lg bg-emerald-700 px-5 py-2.5 font-medium text-white hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 focus-visible:outline-none">Kirim laporan</button>
    </form>
</x-layout>
