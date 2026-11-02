// Widget sidebar: jam, sholat berikutnya, jadwal hari ini, dan cuaca.
// Lokasi diminta saat pertama masuk sampai pengguna menjawab, lalu disimpan; ditolak → Jakarta.
import { hitungJadwal, NAMA, sholatBerikutnya } from './hitung';

const widget = document.querySelector('[data-widget-sholat]');

const JAKARTA = { lintang: -6.1754, bujur: 106.8272, kota: 'Jakarta' };
const CUACA_BERLAKU = 30 * 60_000;

const simpan = {
    ambil(kunci) {
        try {
            return JSON.parse(localStorage.getItem(kunci));
        } catch {
            return null;
        }
    },
    taruh(kunci, nilai) {
        try {
            localStorage.setItem(kunci, JSON.stringify(nilai));
        } catch {
            // Penyimpanan diblokir: widget tetap jalan, hanya tidak mengingat.
        }
    },
};

// Kode cuaca WMO (Open-Meteo) → [kondisi, ikon siang, ikon malam].
function kondisiCuaca(kode) {
    if (kode === 0) return ['Cerah', '☀️', '🌙'];
    if (kode <= 2) return ['Cerah berawan', '🌤️', '☁️'];
    if (kode === 3) return ['Berawan', '☁️', '☁️'];
    if (kode <= 48) return ['Berkabut', '🌫️', '🌫️'];
    if (kode <= 57) return ['Gerimis', '🌦️', '🌧️'];
    if (kode <= 67) return ['Hujan', '🌧️', '🌧️'];
    if (kode <= 77) return ['Salju', '❄️', '❄️'];
    if (kode <= 82) return ['Hujan lokal', '🌦️', '🌧️'];
    return ['Badai petir', '⛈️', '⛈️'];
}

if (widget) {
    const el = (nama) => widget.querySelector(`[data-${nama}]`);
    const pilihMazhab = el('mazhab');
    const pesan = el('lokasi-pesan');
    const jam = (tanggal) => tanggal.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    // Umm al-Qura; bisa selisih sehari dengan penetapan Kemenag (rukyat). Browser tanpa kalender Islam → baris disembunyikan.
    const hijriah = new Intl.DateTimeFormat('id-ID-u-ca-islamic-umalqura', { day: 'numeric', month: 'long', year: 'numeric' });
    const adaHijriah = hijriah.resolvedOptions().calendar === 'islamic-umalqura';

    let lokasi = simpan.ambil('sholat:lokasi') ?? JAKARTA;
    pilihMazhab.value = simpan.ambil('sholat:mazhab') ?? 'syafii';

    const beriPesan = (teks) => {
        pesan.textContent = teks;
        pesan.hidden = !teks;
    };

    function perbarui() {
        const sekarang = new Date();
        const jadwal = hitungJadwal(lokasi.lintang, lokasi.bujur, sekarang, pilihMazhab.value);
        const berikut = sholatBerikutnya(lokasi.lintang, lokasi.bujur, sekarang, pilihMazhab.value);
        const sisa = Math.ceil((berikut.waktu - sekarang) / 60_000);
        const sisaTeks = sisa >= 60 ? `${Math.floor(sisa / 60)} jam ${sisa % 60} menit` : `${sisa} menit`;

        el('jam').textContent = jam(sekarang);
        el('tanggal').textContent = sekarang.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' });
        if (adaHijriah) el('hijriah').textContent = hijriah.format(sekarang);
        el('kota').textContent = lokasi.kota ?? 'Lokasimu';
        el('info-berikutnya').textContent = `${NAMA[berikut.kunci]} dalam ${sisaTeks} (${jam(berikut.waktu)})`;

        widget.querySelectorAll('[data-waktu]').forEach((baris) => {
            baris.querySelector('[data-jam-sholat]').textContent = jam(jadwal[baris.dataset.waktu]);
            const hariIni = berikut.waktu.toDateString() === sekarang.toDateString();
            baris.toggleAttribute('data-sekarang', hariIni && baris.dataset.waktu === berikut.kunci);
        });

        return jadwal;
    }

    async function perbaruiCuaca() {
        const { lintang, bujur } = lokasi;
        let cuaca = simpan.ambil('sholat:cuaca');
        const masihBerlaku = cuaca && cuaca.lintang === lintang && cuaca.bujur === bujur && Date.now() - cuaca.waktu < CUACA_BERLAKU;

        if (!masihBerlaku) {
            try {
                const respons = await fetch(
                    `https://api.open-meteo.com/v1/forecast?latitude=${lintang}&longitude=${bujur}&current=temperature_2m,weather_code&timezone=auto`,
                );
                const { current } = await respons.json();
                cuaca = { lintang, bujur, waktu: Date.now(), suhu: Math.round(current.temperature_2m), kode: current.weather_code };
                simpan.taruh('sholat:cuaca', cuaca);
            } catch {
                return; // cuaca opsional; jadwal tetap tampil
            }
        }

        const jadwal = hitungJadwal(lintang, bujur, new Date(), pilihMazhab.value);
        const malam = new Date() >= jadwal.maghrib || new Date() < jadwal.terbit;
        const [kondisi, ikonSiang, ikonMalam] = kondisiCuaca(cuaca.kode);
        el('cuaca-ikon').textContent = malam ? ikonMalam : ikonSiang;
        el('cuaca-suhu').textContent = `${cuaca.suhu}°C`;
        el('cuaca-kondisi').textContent = kondisi;
        el('cuaca').hidden = false;
    }

    async function namaKota(lintang, bujur) {
        try {
            const respons = await fetch(
                `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lintang}&longitude=${bujur}&localityLanguage=id`,
            );
            const data = await respons.json();
            return data.city || data.locality || data.principalSubdivision || null;
        } catch {
            return null;
        }
    }

    function mintaLokasi() {
        if (!navigator.geolocation) return beriPesan('Browser ini tidak mendukung lokasi. Jadwal memakai Jakarta.');
        // Di luar HTTPS browser menolak tanpa bertanya; jangan dianggap pengguna sudah menjawab.
        if (!window.isSecureContext) return beriPesan('Lokasi hanya bisa dipakai lewat koneksi aman (HTTPS). Jadwal memakai Jakarta.');

        beriPesan('Mencari lokasimu…');
        navigator.geolocation.getCurrentPosition(
            async ({ coords }) => {
                simpan.taruh('sholat:sudah-ditanya', true);
                // Dibulatkan ±1 km: cukup untuk jadwal sholat, tidak membocorkan lokasi persis ke layanan cuaca.
                const lintang = Math.round(coords.latitude * 100) / 100;
                const bujur = Math.round(coords.longitude * 100) / 100;
                lokasi = { lintang, bujur, kota: null };
                perbarui();
                lokasi.kota = await namaKota(lintang, bujur);
                simpan.taruh('sholat:lokasi', lokasi);
                beriPesan('');
                perbarui();
                perbaruiCuaca();
            },
            (galat) => {
                // Hanya penolakan pengguna yang diingat; timeout/GPS gagal dicoba lagi di kunjungan berikutnya.
                if (galat.code === galat.PERMISSION_DENIED) simpan.taruh('sholat:sudah-ditanya', true);
                beriPesan(
                    galat.code === galat.PERMISSION_DENIED
                        ? 'Izin lokasi ditolak, jadwal memakai Jakarta. Izinkan lokasi di pengaturan browser lalu tekan "Gunakan lokasiku".'
                        : 'Lokasi tidak ditemukan, jadwal memakai Jakarta. Coba lagi nanti.',
                );
            },
            { timeout: 15_000, maximumAge: 24 * 60 * 60_000 },
        );
    }

    el('minta-lokasi').addEventListener('click', mintaLokasi);
    pilihMazhab.addEventListener('change', () => {
        simpan.taruh('sholat:mazhab', pilihMazhab.value);
        perbarui();
    });

    perbarui();
    perbaruiCuaca();
    setInterval(perbarui, 15_000);
    if (!simpan.ambil('sholat:lokasi') && !simpan.ambil('sholat:sudah-ditanya')) mintaLokasi();
}
