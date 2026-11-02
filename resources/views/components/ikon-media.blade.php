{{-- Ikon kontrol pemutar (SVG, warna ikut currentColor). Karakter ▶ ⏸ ⏮ ⏭ tampil sebagai kotak emoji biru di Windows. --}}
@props(['nama'])
<svg {{ $attributes->class('size-5 shrink-0') }} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
    @switch($nama)
        @case('putar')
            <path d="M8 5.6v12.8a1 1 0 0 0 1.53.85l10.2-6.4a1 1 0 0 0 0-1.7L9.53 4.75A1 1 0 0 0 8 5.6Z"/>
            @break
        @case('jeda')
            <rect x="6" y="5" width="4" height="14" rx="1.25"/>
            <rect x="14" y="5" width="4" height="14" rx="1.25"/>
            @break
        @case('sebelumnya')
            <rect x="5" y="5" width="2.5" height="14" rx="1"/>
            <path d="M19 6.1v11.8a1 1 0 0 1-1.55.83l-8.6-5.9a1 1 0 0 1 0-1.66l8.6-5.9A1 1 0 0 1 19 6.1Z"/>
            @break
        @case('berikutnya')
            <rect x="16.5" y="5" width="2.5" height="14" rx="1"/>
            <path d="M5 6.1v11.8a1 1 0 0 0 1.55.83l8.6-5.9a1 1 0 0 0 0-1.66l-8.6-5.9A1 1 0 0 0 5 6.1Z"/>
            @break
    @endswitch
</svg>
