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
