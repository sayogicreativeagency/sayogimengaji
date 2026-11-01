// Halaman belajar: highlight per kata mengikuti audio (karaoke), ketuk kata untuk dengar, mode hafalan,
// tandai hafal. Audio per ayat: {url qori}{SSS}{AAA}.mp3; waktu kata dari Quran.com ([posisi, mulai ms, selesai ms]).

const wadah = document.querySelector('[data-belajar]');

const simpan = {
    ambil(kunci, bawaan = null) {
        try {
            return JSON.parse(localStorage.getItem(kunci)) ?? bawaan;
        } catch {
            return bawaan;
        }
    },
    taruh(kunci, nilai) {
        try {
            localStorage.setItem(kunci, JSON.stringify(nilai));
        } catch {
            // Penyimpanan diblokir: tetap jalan tanpa mengingat pilihan.
        }
    },
};

if (wadah) {
    const pemutar = document.querySelector('[data-pemutar-belajar]');
    const waktu = JSON.parse(document.querySelector('[data-waktu-kata]').textContent);
    const daftarQori = JSON.parse(pemutar.dataset.qori);
    const surat = Number(wadah.dataset.surat);
    const jumlah = Number(wadah.dataset.jumlah);
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const el = (nama) => pemutar.querySelector(`[data-${nama}]`);
    const [tombolPutar, status, pilihQori, pilihUlang, pilihKecepatan] = [
        'putar', 'status', 'pilih-qori', 'pilih-ulang', 'pilih-kecepatan',
    ].map(el);

    const audio = new Audio();
    audio.preload = 'auto';
    let ayat = null; // ayat yang sedang/terakhir diputar
    let sisaUlang = 0; // -1 = ulang terus
    let berhentiDi = null; // ms; diisi saat hanya memutar satu kata
    let srcSekarang = '';
    const sudahDidengar = new Set();

    const qoriAktif = () => daftarQori.find((q) => q.id === pilihQori.value);
    const tigaDigit = (n) => String(n).padStart(3, '0');
    const urlAyat = (n) => `${qoriAktif().url}${tigaDigit(surat)}${tigaDigit(n)}.mp3`;
    const segmen = (n) => waktu[n]?.[qoriAktif().id] ?? [];
    const artikel = (n) => wadah.querySelector(`[data-ayat="${n}"]`);
    const kataDi = (n, posisi) => artikel(n)?.querySelector(`[data-posisi="${posisi}"]`);
    // Ganti sumber audio hanya bila berbeda; true = baru dimuat (tunggu metadata sebelum lompat waktu).
    const gantiSrc = (url) => {
        if (srcSekarang === url) return false;
        audio.src = srcSekarang = url;
        return true;
    };
    const jumlahUlang = () => (pilihUlang.value === '0' ? -1 : Number(pilihUlang.value) - 1);
    // defaultPlaybackRate ikut diset karena browser mereset playbackRate setiap ganti src.
    const aturKecepatan = () => (audio.defaultPlaybackRate = audio.playbackRate = Number(pilihKecepatan.value));

    pilihQori.value = simpan.ambil('belajar:qori', daftarQori[0].id);
    if (!qoriAktif()) pilihQori.value = daftarQori[0].id;
    aturKecepatan();

    // ---- Mode hafalan ----
    const pilihanMode = document.querySelectorAll('[data-pilih-mode]');
    const aturMode = (mode) => {
        wadah.dataset.mode = mode;
        wadah.querySelectorAll('.kata[data-terbuka]').forEach((k) => k.removeAttribute('data-terbuka'));
    };
    const modeTersimpan = simpan.ambil('belajar:mode', 'lengkap');
    pilihanMode.forEach((r) => {
        r.checked = r.value === modeTersimpan;
        r.addEventListener('change', () => {
            aturMode(r.value);
            simpan.taruh('belajar:mode', r.value);
        });
    });
    aturMode(modeTersimpan);

    // ---- Highlight ----
    let kataAktif = null;
    const tandaiKata = (tombol) => {
        if (tombol === kataAktif) return;
        kataAktif?.removeAttribute('data-aktif');
        tombol?.setAttribute('data-aktif', '');
        kataAktif = tombol;
    };
    const tandaiAyat = (n, gulir = true) => {
        wadah.querySelectorAll('article[data-aktif]').forEach((a) => a.removeAttribute('data-aktif'));
        artikel(n)?.setAttribute('data-aktif', '');
        if (gulir) artikel(n)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    // Ikuti posisi audio tiap frame: kata yang waktunya sedang berjalan diberi highlight.
    const ikuti = () => {
        if (audio.paused || ayat === null) return;
        const ms = audio.currentTime * 1000;

        if (berhentiDi !== null && ms >= berhentiDi) {
            audio.pause();
            berhentiDi = null;
            return;
        }

        const kini = segmen(ayat).findLast(([, mulai]) => mulai <= ms);
        tandaiKata(kini ? kataDi(ayat, kini[0]) : null);
        requestAnimationFrame(ikuti);
    };

    // ---- Progres ----
    const kirimProgres = (n, statusProgres) =>
        fetch(wadah.dataset.urlProgres, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ surat, ayat: n, status: statusProgres }),
        });

    // ---- Pemutaran ----
    const putar = (n) => {
        ayat = n;
        berhentiDi = null;
        sisaUlang = jumlahUlang();
        if (!gantiSrc(urlAyat(n))) audio.currentTime = 0;
        audio.play().catch(() => {}); // diblokir browser: pengguna tinggal menekan putar
        tandaiAyat(n);
        status.textContent = `Ayat ${n} dari ${jumlah}`;
    };

    const putarKata = (n, posisi) => {
        const s = segmen(n).find(([p]) => p === posisi);
        if (!s) return;
        ayat = n;
        tandaiAyat(n, false);
        const mulai = () => {
            audio.currentTime = s[1] / 1000;
            berhentiDi = s[2];
            audio.play().catch(() => {});
        };
        if (gantiSrc(urlAyat(n))) audio.addEventListener('loadedmetadata', mulai, { once: true });
        else mulai();
        status.textContent = `Ayat ${n}, kata ${posisi}`;
    };

    audio.addEventListener('play', () => {
        tombolPutar.textContent = '⏸';
        tombolPutar.setAttribute('aria-label', 'Jeda');
        requestAnimationFrame(ikuti);
    });
    audio.addEventListener('pause', () => {
        tombolPutar.textContent = '▶';
        tombolPutar.setAttribute('aria-label', 'Putar');
    });
    audio.addEventListener('error', () => (status.textContent = 'Audio gagal dimuat. Coba qori lain.'));
    audio.addEventListener('ended', () => {
        tandaiKata(null);
        if (berhentiDi !== null) {
            berhentiDi = null; // kata terakhir yang diketuk habis bersama file: jangan lanjut ke ayat berikutnya
            return;
        }
        if (!sudahDidengar.has(ayat)) {
            sudahDidengar.add(ayat);
            kirimProgres(ayat, 'didengar');
        }
        if (sisaUlang !== 0) {
            sisaUlang--;
            audio.currentTime = 0;
            audio.play().catch(() => {});
        } else if (ayat < jumlah) {
            putar(ayat + 1);
        } else {
            status.textContent = 'Selesai';
        }
    });

    // Mulai dari #ayat-N (tautan "Lanjutkan"), tanpa memutar otomatis.
    const dariHash = Number(location.hash.match(/^#ayat-(\d+)$/)?.[1]);
    const awal = dariHash >= 1 && dariHash <= jumlah ? dariHash : 1;
    if (awal !== 1) status.textContent = `Mulai dari ayat ${awal}`;

    tombolPutar.addEventListener('click', () => {
        if (ayat === null) putar(awal);
        else if (audio.paused) {
            berhentiDi = null;
            audio.play().catch(() => {});
        } else audio.pause();
    });
    el('berikutnya').addEventListener('click', () => putar(ayat === null ? awal : Math.min(jumlah, ayat + 1)));
    el('sebelumnya').addEventListener('click', () => putar(Math.max(1, (ayat ?? 1) - 1)));
    wadah.querySelectorAll('[data-putar-ayat]').forEach((b) => b.addEventListener('click', () => putar(Number(b.dataset.putarAyat))));
    wadah.querySelectorAll('.kata').forEach((k) =>
        k.addEventListener('click', () => {
            const n = Number(k.closest('[data-ayat]').dataset.ayat);
            if (wadah.dataset.mode !== 'lengkap') k.toggleAttribute('data-terbuka', true);
            putarKata(n, Number(k.dataset.posisi));
        }),
    );
    pilihKecepatan.addEventListener('change', aturKecepatan);
    pilihUlang.addEventListener('change', () => {
        if (ayat !== null) sisaUlang = jumlahUlang();
    });
    pilihQori.addEventListener('change', () => {
        simpan.taruh('belajar:qori', pilihQori.value);
        if (ayat !== null && !audio.paused) putar(ayat);
    });

    // ---- Tandai hafal ----
    const jumlahHafal = document.querySelector('[data-jumlah-hafal]');
    wadah.querySelectorAll('[data-tandai-hafal]').forEach((tombol) =>
        tombol.addEventListener('click', async () => {
            const n = Number(tombol.dataset.tandaiHafal);
            const hafal = tombol.getAttribute('aria-pressed') !== 'true';
            const ubah = (nilai) => {
                tombol.setAttribute('aria-pressed', String(nilai));
                artikel(n).toggleAttribute('data-hafal', nilai);
                jumlahHafal.textContent = wadah.querySelectorAll('article[data-hafal]').length;
            };
            ubah(hafal);
            const respons = await kirimProgres(n, hafal ? 'hafal' : 'belum-hafal').catch(() => null);
            if (!respons?.ok) {
                ubah(!hafal);
                status.textContent = 'Gagal menyimpan. Periksa koneksi lalu coba lagi.';
            }
        }),
    );
}
