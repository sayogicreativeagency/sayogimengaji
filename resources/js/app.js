// Filter hasil pencarian di browser: hanya menyembunyikan kartu, tanpa reload atau panggilan AI baru.
const filter = document.querySelector('[data-filter-hasil]');

if (filter) {
    const derajat = filter.querySelector('#filter-derajat');
    const kitab = filter.querySelector('#filter-kitab');
    const cocok = (el) =>
        (!derajat.value || el.dataset.derajat === derajat.value) && (!kitab.value || el.dataset.kitab === kitab.value);

    filter.addEventListener('change', () => {
        document.querySelectorAll('[data-hadist]').forEach((el) => (el.hidden = !cocok(el)));
        document
            .querySelectorAll('[data-toc-grup]')
            .forEach((grup) => (grup.hidden = !grup.querySelector('[data-hadist]:not([hidden])')));

        const tampil = document.querySelectorAll('article[data-hadist]:not([hidden])').length;
        filter.querySelector('[data-jumlah]').textContent = tampil;
        document.querySelector('[data-filter-kosong]').hidden = tampil > 0;
    });
}
