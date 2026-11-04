<x-layout judul="Kebijakan Privasi" :sidebar="false" deskripsi="Data apa yang dikumpulkan Sayogi Mengaji, untuk apa, berapa lama disimpan, dan hakmu atas data pribadimu.">
    <article class="prosa mx-auto max-w-3xl">
        <h1 class="text-4xl">Kebijakan Privasi</h1>
        <p class="text-sm text-stone-500">Berlaku sejak 4 November 2026</p>

        <p>Kebijakan ini menjelaskan data apa yang dikumpulkan Sayogi Mengaji, untuk apa, dan ke mana data itu dikirim. Kami berusaha mengumpulkan sesedikit mungkin. Sayogi Mengaji tidak memasang iklan dan tidak memakai alat pelacak atau analitik pihak ketiga.</p>

        <h2>1. Data yang kami kumpulkan</h2>
        <h3>Akun</h3>
        <p>Nama, email, dan password (disimpan dalam bentuk hash, sehingga kami tidak bisa membacanya), serta peran akun dan waktu pendaftaran.</p>

        <h3>Belajar Al-Qur'an &amp; langganan</h3>
        <p>Ayat yang sudah kamu dengar dan tandai hafal, beserta waktunya, disimpan di akunmu agar progres bisa dilanjutkan dari perangkat mana pun. Untuk langganan kami menyimpan masa berlakunya dan cara pemberiannya. Saat kamu membayar, kami menyimpan nominal, metode, nama pengirim, dan bukti bayar yang kamu unggah; bukti bayar hanya bisa dilihat admin untuk mencocokkan pembayaran.</p>

        <h3>Formulir kontak &amp; laporan</h3>
        <p>Nama dan email (keduanya opsional), pencarian terkait, dan isi pesan yang kamu kirim.</p>

        <h3>Pencarian hadist</h3>
        <p>Kata yang kamu cari dikirim ke penyedia AI (lihat bagian 3) untuk dikembangkan menjadi kata kunci dan untuk menyusun ringkasan. Hasilnya kami simpan sementara di cache selama 7 hari agar pencarian yang sama tidak memanggil AI lagi. Cache ini tidak dikaitkan dengan akunmu. <strong>Jangan menuliskan data pribadi di kotak pencarian.</strong></p>
        <p>Kami juga mencatat kata yang dicari beserta jumlahnya per hari, tanpa akun dan tanpa alamat IP, untuk menyusun sugesti pencarian dan menemukan topik yang belum terjawab. Kata yang sering dicari bisa tampil sebagai sugesti di beranda setelah dirapikan dengan bantuan AI; kata yang dicari kurang dari tiga kali dalam sebulan tidak pernah ditampilkan.</p>

        <h3>Statistik murottal</h3>
        <p>Saat kamu memutar murottal di halaman surat atau beranda, kami mencatat <strong>alamat IP</strong>, surat, ayat awal dan ayat terakhir yang diputar, apakah surat didengar sampai tamat, lama mendengarkan, qori, halaman asal, jenis perangkat (HP atau komputer), dan akunmu bila sedang masuk. Data ini hanya bisa dilihat superadmin, dipakai untuk memahami surat mana yang paling sering didengar dan di mana pendengar berhenti, dan tidak dibagikan ke pihak ketiga.</p>

        <h3>Data teknis</h3>
        <p>Saat kamu membuka situs, server mencatat <strong>alamat IP</strong> dan jenis peramban (user agent) di data sesi. Alamat IP juga dipakai untuk membatasi percobaan login dan pengiriman formulir agar tidak disalahgunakan, serta dapat tercatat di log server.</p>

        <h3>Lokasi</h3>
        <p>Lokasi hanya diambil <strong>jika kamu mengizinkannya</strong> di peramban, untuk menghitung jadwal sholat dan menampilkan cuaca. Koordinat dibulatkan sekitar 1 km dan disimpan <strong>hanya di peramban kamu</strong>, tidak di server kami. Kalau kamu menolak, jadwal memakai Jakarta.</p>

        <h3>Disimpan di peramban kamu</h3>
        <p>Pilihan qori, posisi terakhir murottal, tampilan latin/terjemahan, mazhab Ashar, lokasi, dan cache cuaca disimpan di penyimpanan lokal peramban (localStorage). Data ini tidak dikirim ke server kami, kecuali qori dan ayat yang sedang diputar untuk statistik murottal di atas, dan bisa kamu hapus dengan membersihkan data situs di peramban.</p>

        <h2>2. Cookie</h2>
        <p>Kami hanya memakai cookie yang diperlukan agar situs berfungsi: cookie sesi, token keamanan formulir (CSRF), dan cookie "ingat saya" bila kamu mencentangnya saat masuk.</p>

        <h2>3. Pihak ketiga</h2>
        <ul>
            <li><strong>DeepSeek</strong> (penyedia AI): menerima kata pencarian dan cuplikan hadist untuk menyusun ringkasan. Menurut kebijakan mereka, data diproses dan disimpan di <strong>Tiongkok</strong> dan dapat dipakai untuk mengembangkan layanan mereka. Data akun dan kontakmu tidak kami kirim ke DeepSeek.</li>
            <li><strong>Open-Meteo</strong> (cuaca) dan <strong>BigDataCloud</strong> (nama kota): bila kamu mengizinkan lokasi, peramban kamu mengirim koordinat yang sudah dibulatkan langsung ke layanan ini.</li>
            <li><strong>equran.id</strong>, <strong>Quran.com</strong>, dan <strong>QuranicAudio</strong>: audio murottal dan audio di halaman belajar diputar langsung dari server mereka, sehingga alamat IP-mu terlihat oleh mereka saat audio dimuat.</li>
            <li><strong>DANA, Saweria, dan aplikasi bank/e-wallet</strong>: bila kamu berinfaq atau membayar langganan, pembayaran diproses oleh mereka sesuai kebijakan privasi masing-masing. Kami tidak menerima data kartu, PIN, atau kata sandi; untuk langganan kami hanya menyimpan yang kamu isi di konfirmasi pembayaran (nama pengirim dan bukti bayar, yang bisa memuat nama dan nomor rekening pengirim).</li>
        </ul>

        <h2>4. Untuk apa data digunakan</h2>
        <ul>
            <li>Menjalankan layanan: login, pencarian, murottal, dan jadwal sholat.</li>
            <li>Menindaklanjuti laporan dan memperbaiki data.</li>
            <li>Memahami pemakaian murottal lewat statistik internal.</li>
            <li>Menjaga keamanan situs dan mencegah penyalahgunaan.</li>
        </ul>
        <p>Kami tidak menjual data pribadimu dan tidak memakainya untuk iklan.</p>

        <h2>5. Penyimpanan &amp; keamanan</h2>
        <p>Password disimpan dalam bentuk hash. Akses ke data di panel admin dibatasi sesuai peran. Data akun dan progres belajar disimpan selama akunmu aktif (progres tetap tersimpan meski langganan berakhir); laporan disimpan sampai selesai ditindaklanjuti; cache pencarian terhapus otomatis setelah 7 hari; catatan kata pencarian harian dan statistik murottal terhapus setelah 90 hari; data sesi kedaluwarsa setelah tidak aktif.</p>

        <h2>6. Hak kamu</h2>
        <p>Sesuai Undang-Undang Pelindungan Data Pribadi (UU No. 27 Tahun 2022), kamu berhak meminta akses, perbaikan, atau penghapusan data pribadimu, termasuk penghapusan akun, serta menarik persetujuan. Izin lokasi bisa dicabut kapan saja dari pengaturan peramban. Ajukan permintaan lewat <a href="/kontak">halaman kontak</a>.</p>

        <h2>7. Anak-anak</h2>
        <p>Pengguna di bawah 17 tahun sebaiknya mendaftar dengan sepengetahuan orang tua atau wali.</p>

        <h2>8. Perubahan kebijakan</h2>
        <p>Kebijakan ini dapat diperbarui, misalnya saat ada fitur atau penyedia layanan baru. Tanggal berlaku di atas akan ikut diperbarui.</p>

        <p>Lihat juga <a href="/syarat-ketentuan">Syarat &amp; Ketentuan</a> dan <a href="/sumber">Sumber &amp; Atribusi</a>.</p>
    </article>
</x-layout>
