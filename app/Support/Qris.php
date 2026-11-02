<?php

namespace App\Support;

/** Payload QRIS (EMVCo): cek CRC & baca nama penerima. Dipakai papan donasi untuk menggambar ulang QR infaq. */
final readonly class Qris
{
    public function __construct(public string $payload) {}

    public function valid(): bool
    {
        return str_starts_with($this->payload, '000201')
            && strlen($this->payload) > 8
            && substr($this->payload, -8, 4) === '6304'
            && strtoupper(substr($this->payload, -4)) === self::crc(substr($this->payload, 0, -4));
    }

    /** Tag 59 (nama merchant/penerima) di tingkat teratas. */
    public function namaPenerima(): ?string
    {
        $nama = $this->tag()['59'] ?? null;

        return $nama === null ? null : trim($nama);
    }

    /** QRIS statis → dinamis dengan nominal terisi (tag 01 = 12, tag 54 = nominal), CRC dihitung ulang. */
    public function denganNominal(int $rupiah): self
    {
        $tag = $this->tag();
        unset($tag['63']);
        $tag['01'] = '12';
        $tag['54'] = (string) $rupiah;
        ksort($tag, SORT_STRING);

        $isi = '';
        foreach ($tag as $id => $nilai) {
            $isi .= $id.sprintf('%02d', strlen($nilai)).$nilai;
        }

        return new self(self::denganCrc($isi.'6304'));
    }

    /** @return array<string, string> tag tingkat teratas → nilai */
    private function tag(): array
    {
        $tag = [];
        for ($i = 0; $i + 4 <= strlen($this->payload); $i += 4 + $panjang) {
            $panjang = (int) substr($this->payload, $i + 2, 2);
            $tag[substr($this->payload, $i, 2)] = substr($this->payload, $i + 4, $panjang);
        }

        return $tag;
    }

    /** Lengkapi payload yang diakhiri "6304" dengan CRC-nya. */
    public static function denganCrc(string $tanpaCrc): string
    {
        return $tanpaCrc.self::crc($tanpaCrc);
    }

    /** CRC-16/CCITT-FALSE sesuai spesifikasi QRIS. */
    private static function crc(string $data): string
    {
        $crc = 0xFFFF;
        foreach (str_split($data) as $karakter) {
            $crc ^= ord($karakter) << 8;
            for ($bit = 0; $bit < 8; $bit++) {
                $crc = $crc & 0x8000 ? (($crc << 1) ^ 0x1021) & 0xFFFF : ($crc << 1) & 0xFFFF;
            }
        }

        return sprintf('%04X', $crc);
    }
}
