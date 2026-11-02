{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($halaman as $alamat)
<url><loc>{{ url($alamat) }}</loc></url>
@endforeach
@foreach ($surat as $nomor)
<url><loc>{{ url('/quran/'.$nomor) }}</loc></url>
@endforeach
@php($dasar = url('/'))
@foreach ($hadist as $h)
<url><loc>{{ $dasar.'/hadist/'.$h->kitab->value.'/'.rawurlencode($h->nomor) }}</loc></url>
@endforeach
</urlset>
