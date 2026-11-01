// Hitung jadwal sholat di browser (tanpa API) dengan parameter Kemenag RI.
import { CalculationParameters, Coordinates, Madhab, PrayerTimes, Rounding } from 'adhan';

// Subuh 20°, Isya 18° + ihtiyat (menit), dikalibrasi ke jadwal Kemenag 5 kota (selisih ≤ 1 menit, condong aman:
// terbit tidak pernah lebih lambat, waktu sholat tidak pernah lebih awal).
// Kalibrasi ulang di tests/js/hitung.test.mjs bila jadwal resmi bergeser.
export const IHTIYAT = { fajr: 2, sunrise: -4, dhuhr: 3, asr: 2, maghrib: 3, isha: 2 };
export const JEDA_IMSAK = 10; // menit sebelum Subuh

export const NAMA = { imsak: 'Imsak', subuh: 'Subuh', terbit: 'Terbit', dzuhur: 'Dzuhur', ashar: 'Ashar', maghrib: 'Maghrib', isya: 'Isya' };

/** @returns {Record<keyof NAMA, Date>} */
export function hitungJadwal(lintang, bujur, tanggal = new Date(), mazhab = 'syafii') {
    const params = new CalculationParameters('Other', 20, 18);
    params.madhab = mazhab === 'hanafi' ? Madhab.Hanafi : Madhab.Shafi;
    params.adjustments = { ...IHTIYAT };
    params.rounding = Rounding.Up; // Kemenag membulatkan ke atas

    const w = new PrayerTimes(new Coordinates(lintang, bujur), tanggal, params);

    return {
        imsak: new Date(w.fajr.getTime() - JEDA_IMSAK * 60_000),
        subuh: w.fajr,
        terbit: w.sunrise,
        dzuhur: w.dhuhr,
        ashar: w.asr,
        maghrib: w.maghrib,
        isya: w.isha,
    };
}

/** Sholat wajib berikutnya setelah `sekarang` (Isya lewat → Subuh besok). */
export function sholatBerikutnya(lintang, bujur, sekarang = new Date(), mazhab = 'syafii') {
    const wajib = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
    const hariIni = hitungJadwal(lintang, bujur, sekarang, mazhab);
    const kunci = wajib.find((k) => hariIni[k] > sekarang);
    if (kunci) return { kunci, waktu: hariIni[kunci] };

    const besok = new Date(sekarang);
    besok.setDate(besok.getDate() + 1);
    return { kunci: 'subuh', waktu: hitungJadwal(lintang, bujur, besok, mazhab).subuh };
}
