<x-layout judul="Sumber & Atribusi" :sidebar="false" deskripsi="Sumber data hadist, Al-Qur'an, murottal, jadwal sholat, cuaca, dan kalender yang dipakai Sayogi Mengaji beserta lisensinya.">
    <article class="prosa mx-auto max-w-3xl">
        <h1 class="text-4xl">Sumber &amp; Atribusi</h1>
        <p>Sayogi Mengaji berdiri di atas karya banyak orang. Berikut sumber data, layanan, dan perangkat lunak yang kami gunakan beserta lisensinya. Jazakumullahu khairan.</p>

        <h2>Hadist</h2>
        <ul>
            <li><a href="https://github.com/fawazahmed0/hadith-api">fawazahmed0/hadith-api</a> (Unlicense): teks Arab, terjemahan Indonesia, dan penilaian derajat dari para ulama untuk Shahih Bukhari, Shahih Muslim, Sunan Abu Dawud, Sunan Tirmidzi, Sunan Nasa'i, Sunan Ibnu Majah, dan Muwaththa' Malik. Penomoran hadist mengikuti dataset ini dan bisa berbeda dari kitab cetak.</li>
        </ul>

        <h2>Al-Qur'an</h2>
        <ul>
            <li><a href="https://equran.id">equran.id</a>: teks Al-Qur'an, transliterasi latin, terjemahan Kementerian Agama RI, deskripsi surat, dan audio murottal.</li>
            <li><a href="https://quran.com">Quran.com</a> (Quran Foundation): teks Utsmani per kata dan waktu tiap kata untuk fitur belajar, dengan audio dari <a href="https://quran.com">Quran.com</a> dan <a href="https://quranicaudio.com">QuranicAudio</a> (Mahmoud Khalil Al-Husary Muallim dan Misyari Rasyid Al-Afasi).</li>
            <li>Qori murottal: Abdullah Al-Juhany, Abdul Muhsin Al-Qasim, Abdurrahman As-Sudais, Ibrahim Al-Dossari, Misyari Rasyid Al-Afasi, dan Yasser Al-Dosari.</li>
        </ul>

        <h2>Jadwal sholat</h2>
        <ul>
            <li>Dihitung dengan pustaka <a href="https://github.com/batoulapps/adhan-js">adhan</a> (MIT, Batoul Apps) memakai parameter Kemenag RI (Subuh 20°, Isya 18°, dengan ihtiyat), dan dicocokkan dengan jadwal resmi Kemenag dari <a href="https://api.myquran.com">api.myquran.com</a>.</li>
        </ul>

        <h2>Kalender</h2>
        <ul>
            <li>Tanggal Hijriah dan hari besar Islam dihitung dengan kalender Umm al-Qura dari pustaka ICU (bawaan PHP dan browser).</li>
            <li>Libur nasional dan cuti bersama dari kalender publik Google "Hari libur di Indonesia".</li>
        </ul>

        <h2>Cuaca &amp; lokasi</h2>
        <ul>
            <li><a href="https://open-meteo.com/">Weather data by Open-Meteo.com</a>, dilisensikan dengan <a href="https://creativecommons.org/licenses/by/4.0/">CC BY 4.0</a>. Data ditampilkan sebagai suhu dan kondisi cuaca saat ini.</li>
            <li>Nama kota dari koordinat: <a href="https://www.bigdatacloud.com">BigDataCloud</a> (API reverse geocoding gratis sisi klien).</li>
        </ul>

        <h2>Kecerdasan buatan</h2>
        <ul>
            <li><a href="https://www.deepseek.com">DeepSeek</a> untuk mengembangkan kata kunci dan menyusun ringkasan, melalui <a href="https://laravel.com/docs/ai-sdk">Laravel AI SDK</a>.</li>
        </ul>

        <h2>Huruf</h2>
        <ul>
            <li>Amiri dan Amiri Quran (Khaled Hosny), Cormorant Garamond (Christian Thalmann), dan Instrument Sans, semuanya dengan SIL Open Font License. Disajikan dari server kami sendiri.</li>
        </ul>

        <h2>Perangkat lunak</h2>
        <ul>
            <li><a href="https://laravel.com">Laravel</a>, <a href="https://filamentphp.com">Filament</a>, dan <a href="https://tailwindcss.com">Tailwind CSS</a> (MIT).</li>
            <li><a href="https://github.com/chillerlan/php-qrcode">chillerlan/php-qrcode</a> (MIT/Apache-2.0) untuk kode QR infaq.</li>
        </ul>

        <h2>Infaq</h2>
        <ul>
            <li>Diproses oleh <a href="https://saweria.co">Saweria</a> dan DANA sesuai ketentuan masing-masing.</li>
        </ul>

        <p>Ada sumber yang belum tercantum atau atribusi yang keliru? Kabari kami lewat <a href="/kontak">halaman kontak</a>.</p>
    </article>
</x-layout>
