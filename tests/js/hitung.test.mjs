// Jalankan: npm run test:js. Acuan: jadwal Kemenag (api.myquran.com) per kota & tanggal.
import assert from 'node:assert/strict';
import test from 'node:test';
import { hitungJadwal, sholatBerikutnya } from '../../resources/js/sholat/hitung.js';

const KOTA = {
    jakarta: [-6.1754, 106.8272, 'Asia/Jakarta'],
    surabaya: [-7.2575, 112.7521, 'Asia/Jakarta'],
    makassar: [-5.1477, 119.4327, 'Asia/Makassar'],
    jayapura: [-2.5337, 140.7181, 'Asia/Jayapura'],
    medan: [3.5952, 98.6722, 'Asia/Jakarta'],
};

// [kota, tanggal, imsak, subuh, terbit, dzuhur, ashar, maghrib, isya]
const KEMENAG = [
    ['jakarta', '2026-01-15', '04:17', '04:27', '05:45', '12:05', '15:30', '18:18', '19:33'],
    ['jakarta', '2026-06-15', '04:29', '04:39', '05:57', '11:57', '15:18', '17:49', '19:04'],
    ['jakarta', '2026-11-01', '03:59', '04:09', '05:23', '11:40', '14:55', '17:49', '19:00'],
    ['surabaya', '2026-11-01', '03:34', '03:44', '04:59', '11:16', '14:30', '17:27', '18:38'],
    ['makassar', '2026-11-01', '04:10', '04:20', '05:34', '11:49', '15:05', '17:58', '19:09'],
    ['jayapura', '2026-11-01', '03:48', '03:58', '05:12', '11:24', '14:42', '17:30', '18:41'],
    ['medan', '2026-11-01', '04:42', '04:52', '06:06', '12:12', '15:32', '18:12', '19:22'],
    ['medan', '2026-06-15', '04:44', '04:54', '06:13', '12:29', '15:55', '18:39', '19:54'],
];
const URUTAN = ['imsak', 'subuh', 'terbit', 'dzuhur', 'ashar', 'maghrib', 'isya'];
const menit = (hhmm) => hhmm.split(':').reduce((j, m) => j * 60 + Number(m));
const jam = (tanggal, tz) => tanggal.toLocaleTimeString('en-GB', { timeZone: tz, hour: '2-digit', minute: '2-digit', hour12: false });

for (const [kota, tanggal, ...acuan] of KEMENAG) {
    test(`${kota} ${tanggal} cocok dengan Kemenag (±1 menit, condong aman)`, () => {
        const [lintang, bujur, tz] = KOTA[kota];
        const jadwal = hitungJadwal(lintang, bujur, new Date(`${tanggal}T12:00:00Z`));

        URUTAN.forEach((k, i) => {
            const selisih = menit(jam(jadwal[k], tz)) - menit(acuan[i]);
            assert.ok(Math.abs(selisih) <= 1, `${k}: ${jam(jadwal[k], tz)} vs Kemenag ${acuan[i]}`);
            if (k === 'terbit') assert.ok(selisih <= 0, `terbit tidak boleh lebih lambat (${selisih})`);
            else assert.ok(selisih >= 0, `${k} tidak boleh lebih awal (${selisih})`);
        });
    });
}

test('Ashar Hanafi lebih akhir dari Syafii', () => {
    const [lintang, bujur] = KOTA.jakarta;
    const hari = new Date('2026-11-01T12:00:00Z');
    assert.ok(hitungJadwal(lintang, bujur, hari, 'hanafi').ashar > hitungJadwal(lintang, bujur, hari, 'syafii').ashar);
});

test('sholat berikutnya setelah Isya adalah Subuh besok', () => {
    const [lintang, bujur] = KOTA.jakarta;
    const malam = new Date('2026-11-01T22:00:00+07:00');
    const { kunci, waktu } = sholatBerikutnya(lintang, bujur, malam);
    assert.equal(kunci, 'subuh');
    assert.deepEqual(waktu, hitungJadwal(lintang, bujur, new Date('2026-11-02T12:00:00+07:00')).subuh);
    assert.equal(sholatBerikutnya(lintang, bujur, new Date('2026-11-01T13:00:00+07:00')).kunci, 'ashar');
});
