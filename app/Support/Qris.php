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
        for ($i = 0; $i + 4 <= strlen($this->payload); $i += 4 + $panjang) {
            $tag = substr($this->payload, $i, 2);
            $panjang = (int) substr($this->payload, $i + 2, 2);
            if ($tag === '59') {
                return trim(substr($this->payload, $i + 4, $panjang));
            }
        }

        return null;
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
