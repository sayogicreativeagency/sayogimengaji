// Murottal: daftar surat (penyaring + "Lanjutkan") dan pemutar per ayat di halaman surat.
// Audio di-stream dari CDN equran.id: {url qori}{SSS}{AAA}.mp3. Ayat 0 = basmalah (memakai file 1:1).

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
            // Penyimpanan diblokir (mode privat): fitur tetap jalan tanpa mengingat.
        }
    },
};

const urlAudioAyat = (urlQori, surat, n) => {
    const tigaDigit = (x) => String(x).padStart(3, '0');

    return urlQori + (n === 0 ? '001001' : tigaDigit(surat) + tigaDigit(n)) + '.mp3';
};

// ---- Daftar surat ----
const saring = document.querySelector('[data-saring-surat]');
if (saring) {
    const kartu = [...document.querySelectorAll('[data-surat-kartu]')];
    saring.addEventListener('input', () => {
        const kata = saring.value.trim().toLowerCase();
        kartu.forEach((k) => (k.hidden = !k.dataset.cari.includes(kata)));
        document.querySelector('[data-saring-kosong]').hidden = kartu.some((k) => !k.hidden);
    });
}

const lanjutkan = document.querySelector('[data-lanjutkan]');
const terakhir = simpan.ambil('murottal:terakhir');
if (lanjutkan && terakhir) {
    lanjutkan.href = `/quran/${terakhir.surat}#ayat-${terakhir.ayat}`;
    lanjutkan.querySelector('[data-lanjutkan-teks]').textContent = `${terakhir.nama} ayat ${terakhir.ayat}`;
    lanjutkan.hidden = false;
}

// ---- Tampilan latin/terjemahan ----
const daftarAyat = document.querySelector('[data-daftar-ayat]');
document.querySelectorAll('[data-tampil]').forEach((kotak) => {
    const atribut = `data-tanpa-${kotak.dataset.tampil}`;
    kotak.checked = simpan.ambil(`murottal:${atribut}`, true);
    daftarAyat.toggleAttribute(atribut, !kotak.checked);
    kotak.addEventListener('change', () => {
        daftarAyat.toggleAttribute(atribut, !kotak.checked);
        simpan.taruh(`murottal:${atribut}`, kotak.checked);
    });
});

// ---- Pemutar ----
const pemutar = document.querySelector('[data-murottal]');
if (pemutar) {
    const surat = Number(pemutar.dataset.surat);
    const jumlah = Number(pemutar.dataset.jumlah);
    const pertama = pemutar.dataset.basmalah === '1' ? 0 : 1;
    const namaSurat = pemutar.dataset.namaSurat;
    const daftarQori = JSON.parse(pemutar.dataset.qori);
    const el = (nama) => pemutar.querySelector(`[data-${nama}]`);
    const [tombolPutar, status, pilihQori, pilihUlang, pilihKecepatan, selesai] = [
        'putar', 'status', 'pilih-qori', 'pilih-ulang', 'pilih-kecepatan', 'selesai',
    ].map(el);

    const audio = new Audio();
    audio.preload = 'auto';
    let ayat = null; // ayat yang sedang/terakhir diputar
    let sisaUlang = 0; // -1 = ulang terus

    const qoriAktif = () => daftarQori.find((q) => q.id === pilihQori.value);
    const urlAyat = (n) => urlAudioAyat(qoriAktif().url, surat, n);
    const label = (n) => (n === 0 ? 'Basmalah' : `Ayat ${n} dari ${jumlah}`);
    const jumlahUlang = () => (pilihUlang.value === '0' ? -1 : Number(pilihUlang.value) - 1);
    // defaultPlaybackRate ikut diset karena browser mereset playbackRate setiap ganti src.
    const aturKecepatan = () => (audio.defaultPlaybackRate = audio.playbackRate = Number(pilihKecepatan.value));

    pilihQori.value = simpan.ambil('murottal:qori', pemutar.dataset.qoriBawaan);
    if (!qoriAktif()) pilihQori.value = pemutar.dataset.qoriBawaan;
    aturKecepatan();

    // Mulai dari #ayat-N bila datang dari tautan/"Lanjutkan" (tanpa memutar otomatis).
    const dariHash = Number(location.hash.match(/^#ayat-(\d+)$/)?.[1]);
    const awal = dariHash >= 1 && dariHash <= jumlah ? dariHash : pertama;
    if (awal !== pertama) status.textContent = `Mulai dari ayat ${awal}`;

    const tandai = (n) => {
        document.querySelectorAll('[data-ayat][data-aktif]').forEach((a) => a.removeAttribute('data-aktif'));
        const target = document.querySelector(`[data-ayat="${n}"]`);
        target?.setAttribute('data-aktif', '');
        target?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    const putar = (n) => {
        ayat = n;
        sisaUlang = n === 0 ? 0 : jumlahUlang();
        if (selesai) selesai.hidden = true;
        audio.src = urlAyat(n);
        audio.play().catch(() => {}); // diblokir browser: pengguna tinggal menekan putar
        tandai(n);
        status.textContent = label(n);
        if (n > 0) simpan.taruh('murottal:terakhir', { surat, nama: namaSurat, ayat: n });
        if ('mediaSession' in navigator) {
            navigator.mediaSession.metadata = new MediaMetadata({
                title: `${namaSurat} · ${label(n)}`,
                artist: qoriAktif().nama,
                album: "Sayogi Mengaji · Al-Qur'an",
            });
        }
    };

    const berikutnya = () => {
        if (ayat === null) putar(awal);
        else if (ayat < jumlah) putar(ayat + 1);
    };
    const sebelumnya = () => putar(Math.max(pertama, (ayat ?? pertama) - 1));
    const putarAtauJeda = () => {
        if (ayat === null) putar(awal);
        // Surat sudah selesai: mulai lagi dari awal, bukan mengulang ayat terakhir.
        else if (audio.ended && ayat === jumlah) putar(pertama);
        else if (audio.paused) audio.play().catch(() => {});
        else audio.pause();
    };

    audio.addEventListener('ended', () => {
        if (sisaUlang !== 0) {
            sisaUlang--;
            audio.currentTime = 0;
            audio.play().catch(() => {});
        } else if (ayat < jumlah) {
            putar(ayat + 1);
        } else {
            status.textContent = 'Selesai';
            if (selesai) selesai.hidden = false;
        }
    });
    audio.addEventListener('play', () => {
        tombolPutar.toggleAttribute('data-main', true); // ikon jeda (lihat tombol data-putar)
        tombolPutar.setAttribute('aria-label', 'Jeda');
    });
    audio.addEventListener('pause', () => {
        tombolPutar.toggleAttribute('data-main', false);
        tombolPutar.setAttribute('aria-label', 'Putar');
    });
    audio.addEventListener('error', () => (status.textContent = 'Audio gagal dimuat. Coba qori lain.'));

    tombolPutar.addEventListener('click', putarAtauJeda);
    el('berikutnya').addEventListener('click', berikutnya);
    el('sebelumnya').addEventListener('click', sebelumnya);
    document.querySelectorAll('[data-putar-ayat]').forEach((b) =>
        b.addEventListener('click', () => putar(Number(b.dataset.putarAyat))),
    );
    pilihKecepatan.addEventListener('change', aturKecepatan);
    pilihUlang.addEventListener('change', () => {
        if (ayat > 0) sisaUlang = jumlahUlang();
    });
    pilihQori.addEventListener('change', () => {
        simpan.taruh('murottal:qori', pilihQori.value);
        if (ayat === null) return;
        const sedangJalan = !audio.paused;
        audio.src = urlAyat(ayat);
        if (sedangJalan) audio.play().catch(() => {});
    });

    if ('mediaSession' in navigator) {
        navigator.mediaSession.setActionHandler('play', putarAtauJeda);
        navigator.mediaSession.setActionHandler('pause', () => audio.pause());
        navigator.mediaSession.setActionHandler('nexttrack', berikutnya);
        navigator.mediaSession.setActionHandler('previoustrack', sebelumnya);
    }
}

// ---- Beranda: pilih surat lewat combobox, putar di mini player ----
const pilihSurat = document.querySelector('[data-pilih-surat]');
if (pilihSurat) {
    const kotak = pilihSurat.querySelector('[role="combobox"]');
    const daftar = pilihSurat.querySelector('[role="listbox"]');
    const kosong = pilihSurat.querySelector('[data-surat-kosong]');
    const opsi = [...daftar.children];
    const mini = document.querySelector('[data-mini-murottal]');
    const el = (nama) => mini.querySelector(`[data-${nama}]`);
    const tombolPutar = el('putar');
    const daftarQori = JSON.parse(mini.dataset.qori);
    const audio = new Audio();
    let surat = null; // { nomor, nama, jumlah, pertama }
    let ayat = null;

    // "al fatihah" dan "alfatihah" sama-sama cocok dengan "Al-Fatihah".
    const bersih = (teks) => teks.toLowerCase().replace(/[^a-z0-9]/g, '');
    const terlihat = () => opsi.filter((o) => !o.hidden);
    const judul = () => (surat ? `${surat.nomor}. ${surat.nama}` : '');

    const sorot = (o) => {
        opsi.forEach((x) => x.removeAttribute('data-sorot'));
        o?.setAttribute('data-sorot', '');
        o?.scrollIntoView({ block: 'nearest' });
        if (o) kotak.setAttribute('aria-activedescendant', o.id);
        else kotak.removeAttribute('aria-activedescendant');
    };
    const buka = () => {
        const ada = terlihat().length > 0;
        daftar.hidden = !ada;
        kosong.hidden = ada;
        kotak.setAttribute('aria-expanded', String(ada));
    };
    const tutup = () => {
        daftar.hidden = kosong.hidden = true;
        kotak.setAttribute('aria-expanded', 'false');
        sorot(null);
    };
    const saring = (teks) => {
        const kata = bersih(teks);
        opsi.forEach((o) => (o.hidden = !bersih(o.dataset.cari).includes(kata)));
    };

    const qoriAktif = () =>
        daftarQori.find((q) => q.id === simpan.ambil('murottal:qori')) ?? daftarQori.find((q) => q.id === mini.dataset.qoriBawaan);
    const label = (n) => (n === 0 ? 'Basmalah' : `Ayat ${n} dari ${surat.jumlah}`);

    const putar = (n) => {
        ayat = n;
        audio.src = urlAudioAyat(qoriAktif().url, surat.nomor, n);
        audio.play().catch(() => {}); // diblokir browser: pengguna tinggal menekan putar
        el('status').textContent = label(n);
        el('progres').style.width = `${(n / surat.jumlah) * 100}%`;
        if (n > 0) simpan.taruh('murottal:terakhir', { surat: surat.nomor, nama: surat.nama, ayat: n });
        if ('mediaSession' in navigator) {
            navigator.mediaSession.metadata = new MediaMetadata({
                title: `${surat.nama} · ${label(n)}`,
                album: "Sayogi Mengaji · Al-Qur'an",
            });
        }
    };
    const berikutnya = () => {
        if (ayat < surat.jumlah) putar(ayat + 1);
    };
    const sebelumnya = () => putar(Math.max(surat.pertama, ayat - 1));
    const putarAtauJeda = () => {
        if (audio.ended && ayat === surat.jumlah) putar(surat.pertama);
        else if (audio.paused) audio.play().catch(() => {});
        else audio.pause();
    };

    const pilih = (o) => {
        surat = {
            nomor: Number(o.dataset.nomor),
            nama: o.dataset.nama,
            jumlah: Number(o.dataset.jumlah),
            pertama: o.dataset.basmalah === '1' ? 0 : 1,
        };
        opsi.forEach((x) => x.setAttribute('aria-selected', String(x === o)));
        kotak.value = judul();
        kotak.blur();
        el('tautan').textContent = surat.nama;
        el('tautan').href = `/quran/${surat.nomor}`;
        mini.hidden = false;
        putar(surat.pertama);
    };

    kotak.addEventListener('focus', () => {
        kotak.select();
        saring('');
        buka();
    });
    kotak.addEventListener('click', buka);
    kotak.addEventListener('input', () => {
        saring(kotak.value);
        buka();
        sorot(terlihat()[0]);
    });
    kotak.addEventListener('blur', () => {
        kotak.value = judul();
        tutup();
    });
    kotak.addEventListener('keydown', (e) => {
        const ada = terlihat();
        const posisi = ada.findIndex((o) => o.hasAttribute('data-sorot'));

        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            buka();
            const arah = e.key === 'ArrowDown' ? 1 : -1;
            sorot(ada[(posisi + arah + ada.length) % ada.length]);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const dipilih = ada[Math.max(posisi, 0)];
            if (!daftar.hidden && dipilih) pilih(dipilih);
        } else if (e.key === 'Escape') {
            tutup();
        }
    });
    // mousedown dicegah supaya kotak tidak blur (dan daftar menutup) sebelum klik terdaftar.
    daftar.addEventListener('mousedown', (e) => e.preventDefault());
    daftar.addEventListener('click', (e) => {
        const o = e.target.closest('[role="option"]');
        if (o) pilih(o);
    });

    audio.addEventListener('ended', () => {
        if (ayat < surat.jumlah) putar(ayat + 1);
        else el('status').textContent = 'Selesai';
    });
    audio.addEventListener('play', () => {
        tombolPutar.toggleAttribute('data-main', true);
        tombolPutar.setAttribute('aria-label', 'Jeda');
    });
    audio.addEventListener('pause', () => {
        tombolPutar.toggleAttribute('data-main', false);
        tombolPutar.setAttribute('aria-label', 'Putar');
    });
    audio.addEventListener('error', () => {
        if (surat) el('status').textContent = 'Audio gagal dimuat. Coba lagi.';
    });
    tombolPutar.addEventListener('click', putarAtauJeda);
    el('berikutnya').addEventListener('click', berikutnya);
    el('sebelumnya').addEventListener('click', sebelumnya);

    if ('mediaSession' in navigator) {
        navigator.mediaSession.setActionHandler('play', () => surat && putarAtauJeda());
        navigator.mediaSession.setActionHandler('pause', () => audio.pause());
        navigator.mediaSession.setActionHandler('nexttrack', () => surat && berikutnya());
        navigator.mediaSession.setActionHandler('previoustrack', () => surat && sebelumnya());
    }
}
