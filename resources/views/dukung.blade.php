<x-layout judul="Dukung">
    <article class="mx-auto max-w-2xl">
        <h1 class="text-3xl font-semibold tracking-tight">Infaq untuk Hadist Apa</h1>
        <p class="mt-4 text-lg leading-relaxed text-stone-700 dark:text-stone-300">
            Hadist Apa gratis untuk semua orang dan tanpa iklan. Agar tetap begitu, kami mengandalkan
            infaq seikhlasnya dari para pengguna.
        </p>

        <figure class="mt-8 border-l-4 border-emerald-600 pl-5">
            <blockquote class="text-xl leading-relaxed">“Sedekah itu tidak akan mengurangi harta.”</blockquote>
            <figcaption class="mt-2 text-sm text-stone-500">
                <a href="{{ route('hadist', ['muslim', '6592']) }}" class="hover:underline">HR. Muslim no. 6592</a> · Shahih
            </figcaption>
        </figure>

        <h2 class="mt-10 text-lg font-semibold">Untuk apa infaqmu digunakan</h2>
        <ul class="mt-3 space-y-3 leading-relaxed">
            <li><strong>Server dan database</strong> yang menyimpan lebih dari 36 ribu hadist beserta teks Arab dan derajatnya.</li>
            <li><strong>Biaya AI</strong> yang membantu memahami pertanyaanmu dan merangkum hadist yang relevan.</li>
            <li><strong>Pengembangan fitur berikutnya</strong>, insyaAllah: murottal dengan pilihan qori, jadwal sholat, dan belajar Al-Qur'an.</li>
        </ul>

        <h2 class="mt-10 text-lg font-semibold">Ilmu yang terus mengalir</h2>
        <p class="mt-3 leading-relaxed">
            Rasulullah ﷺ bersabda bahwa amal seseorang terputus ketika ia wafat, kecuali tiga perkara; salah satunya
            ilmu yang bermanfaat (<a href="{{ route('hadist', ['muslim', '4223']) }}" class="text-emerald-700 hover:underline dark:text-emerald-400">HR. Muslim no. 4223</a>).
            Setiap kali seseorang menemukan dan mempelajari hadist di sini, semoga Allah mencatat kebaikannya juga
            untukmu yang ikut menjaganya.
        </p>

        <div class="mt-10 rounded-xl border border-emerald-200 bg-emerald-50/60 p-6 text-center dark:border-emerald-900 dark:bg-emerald-950/30">
            @if ($saweria = config('services.saweria.url'))
                <a href="{{ $saweria }}" target="_blank" rel="noopener"
                   class="inline-block rounded-lg bg-emerald-700 px-6 py-3 text-lg font-medium text-white hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 focus-visible:outline-none">
                    Infaq lewat Saweria
                </a>
                <p class="mt-3 text-sm text-stone-600 dark:text-stone-400">Pembayaran diproses oleh Saweria lewat QRIS dan e-wallet. Berapa pun nominalnya sangat berarti.</p>
            @else
                <p class="text-lg font-medium">Segera hadir</p>
                <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">Halaman infaq sedang kami siapkan. Sementara itu, doa dan berbagi Hadist Apa sudah sangat membantu.</p>
            @endif
        </div>

        <p class="mt-6 text-sm leading-relaxed text-stone-600 dark:text-stone-400">
            Infaq ini sepenuhnya untuk operasional dan pengembangan Hadist Apa, bukan penggalangan dana untuk pihak lain.
        </p>
        <p class="mt-4 leading-relaxed">
            <em>Jazakumullahu khairan.</em> Belum bisa berinfaq? Doakan kami dan bagikan Hadist Apa ke orang
            terdekat. Itu juga dukungan yang sangat berarti.
        </p>
    </article>
</x-layout>
