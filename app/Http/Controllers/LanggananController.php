<?php

namespace App\Http\Controllers;

use App\Enums\StatusBayar;
use App\Models\Langganan;
use App\Models\Pembayaran;
use App\Support\Qris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** Beli langganan belajar: pilih paket → bayar QRIS/transfer dengan nominal unik → konfirmasi → dicek admin. */
class LanggananController extends Controller
{
    public function tampil(Request $request)
    {
        abort_unless(Langganan::berbayar(), 404);
        $user = $request->user();

        return view('langganan.index', [
            'harga' => Langganan::harga(),
            'langganan' => $user->berlangganan() ? $user->langganan : null,
            'tagihan' => $user->pembayaran()->terbuka()->latest('id')->first(),
        ]);
    }

    public function pesan(Request $request)
    {
        abort_unless(Langganan::berbayar(), 404);
        $bulan = (int) $request->validate(['bulan' => ['required', Rule::in(Pembayaran::PILIHAN_BULAN)]])['bulan'];

        // Satu tagihan belum-dikonfirmasi per akun; pesan ulang mengganti paketnya.
        $tagihan = $request->user()->pembayaran()->where('status', StatusBayar::Menunggu)->firstOrNew();
        if ($tagihan->bulan !== $bulan) {
            $tagihan->fill(['bulan' => $bulan, 'nominal' => Pembayaran::nominalUnik(Langganan::harga() * $bulan)])->save();
        }

        return redirect('/langganan/bayar/'.$tagihan->id);
    }

    public function bayar(Request $request, Pembayaran $pembayaran)
    {
        abort_unless($pembayaran->user_id === $request->user()->id, 404);
        $qris = new Qris((string) config('services.infaq.qris'));

        return view('langganan.bayar', [
            'tagihan' => $pembayaran,
            'qris' => $qris->valid() ? $qris->denganNominal($pembayaran->nominal) : null,
            'rekening' => config('services.pembayaran.rekening'),
        ]);
    }

    public function konfirmasi(Request $request, Pembayaran $pembayaran)
    {
        abort_unless($pembayaran->user_id === $request->user()->id, 404);
        abort_unless($pembayaran->status->terbuka(), 409);

        $data = $request->validate([
            'metode' => ['required', Rule::in(['qris', 'transfer'])],
            'pengirim' => ['nullable', 'string', 'max:100'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
        ]);

        if ($request->hasFile('bukti')) {
            if ($pembayaran->bukti) {
                Storage::delete($pembayaran->bukti);
            }
            $data['bukti'] = $request->file('bukti')->store('bukti-bayar');
        }
        $pembayaran->update([...$data, 'status' => StatusBayar::Dilaporkan, 'dilaporkan_at' => now()]);

        return back()->with('status', 'Terima kasih, konfirmasimu sudah kami terima. Langganan aktif setelah pembayaran dicek admin.');
    }
}
