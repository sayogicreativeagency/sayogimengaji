import './murottal';
import './sholat/widget';
import './belajar';

// Menu HP: showModal() mengunci fokus di panel dan Esc menutupnya; animasinya di CSS (.menu-hp).
const menuHp = document.getElementById('menu-hp');

if (menuHp) {
    document.querySelector('[data-buka-menu]').addEventListener('click', () => menuHp.showModal());
    menuHp.querySelector('[data-tutup-menu]').addEventListener('click', () => menuHp.close());
    // Layar dilebarkan sampai menu desktop tampil: panel tidak boleh tertinggal terbuka.
    matchMedia('(min-width: 48rem)').addEventListener('change', (e) => e.matches && menuHp.close());
}

// Halaman doa: saring kartu di browser, judul grup ikut hilang kalau semua kartunya tersaring.
const saringDoa = document.querySelector('[data-saring-doa]');

if (saringDoa) {
    const grup = [...document.querySelectorAll('[data-doa-grup]')];

    saringDoa.addEventListener('input', () => {
        const kata = saringDoa.value.trim().toLowerCase();

        grup.forEach((g) => {
            const kartu = [...g.querySelectorAll('[data-doa-kartu]')];
            kartu.forEach((k) => (k.hidden = !k.dataset.cari.includes(kata)));
            g.hidden = kartu.every((k) => k.hidden);
        });
        document.querySelector('[data-saring-doa-kosong]').hidden = grup.some((g) => !g.hidden);
    });
}

// Filter hasil pencarian: muat daftar dari /cari/saring (DB saja, tanpa AI) tanpa reload.
// Ringkasan AI disembunyikan selama filter aktif karena hanya berlaku untuk hasil awal.
const filter = document.querySelector('[data-filter-hasil]');

if (filter) {
    const hasil = document.querySelector('[data-hasil]');
    const ringkasan = document.querySelector('[data-ringkasan]');
    const awal = hasil.innerHTML;
    let permintaan;

    filter.addEventListener('change', async () => {
        const params = new URLSearchParams([...new FormData(filter)].filter(([, nilai]) => nilai));
        permintaan?.abort();

        if (!params.size) {
            ringkasan.hidden = false;
            hasil.innerHTML = awal;
            hasil.removeAttribute('aria-busy');
            return;
        }

        ringkasan.hidden = true;
        params.set('q', filter.dataset.q);
        const ini = (permintaan = new AbortController());
        hasil.setAttribute('aria-busy', 'true');

        try {
            const respons = await fetch(`/cari/saring?${params}`, { signal: ini.signal });
            if (!respons.ok) throw new Error(respons.status);
            hasil.innerHTML = await respons.text();
        } catch (galat) {
            if (galat.name === 'AbortError') return;
            hasil.innerHTML = '<p class="py-10 text-center text-stone-500">Gagal memuat hasil. Coba lagi.</p>';
        }

        if (permintaan === ini) hasil.removeAttribute('aria-busy');
    });
}
