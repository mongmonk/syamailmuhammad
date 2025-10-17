@extends('layouts.app')

@section('title', 'Syamail Muhammad — Mengenal Pribadi Agung Rasulullah ﷺ | Baca Gratis')

@section('meta')
<meta name="description" content="Baca kitab Syamail karya Imam at-Tirmidzi untuk mengenal pribadi agung Rasulullah ﷺ. Aplikasi menyediakan baca per bab, bookmark, catatan, progres, audio, dan pencarian.">
<meta property="og:type" content="website">
<meta property="og:title" content="Syamail Muhammad — Mengenal Pribadi Agung Rasulullah ﷺ">
<meta property="og:description" content="Membaca Syamail secara terstruktur dengan fitur modern: bab, bookmark, catatan, progres, audio, dan pencarian.">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ asset('masjid.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Syamail Muhammad — Mengenal Pribadi Agung Rasulullah ﷺ">
<meta name="twitter:description" content="Baca Syamail secara terstruktur dengan fitur modern.">
<meta name="twitter:image" content="{{ asset('masjid.jpg') }}">
<link rel="preload" as="image" href="{{ asset('masjid.jpg') }}">

<!-- JSON-LD: Book -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Book",
  "name": "Syama'il an-Nabi ﷺ (Syamail Muhammad)",
  "alternateName": "Syamail Muhammadiyah",
  "author": {
    "@@type": "Person",
    "name": "Imam at-Tirmidzi"
  },
  "inLanguage": "id",
  "genre": "Sirah, Hadits",
  "url": "{{ url('/') }}",
  "image": "{{ asset('masjid.jpg') }}",
  "description": "Kumpulan hadits tentang sifat, akhlak, dan kehidupan Nabi Muhammad ﷺ yang disusun oleh Imam at-Tirmidzi.",
  "offers": {
    "@@type": "Offer",
    "price": "100000",
    "priceCurrency": "IDR",
    "availability": "http://schema.org/InStock",
    "url": "{{ 'https://api.whatsapp.com/send?phone=' . \App\Support\PhoneUtil::normalize(config('app.contact_phone')) . '&text=' . rawurlencode('Assalamualaikum kak... Saya ingin berlangganan kitab ' . config('app.name') . '. tolong dibantu') }}"
  }
}
</script>

<!-- JSON-LD: WebSite + SearchAction -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebSite",
  "name": "{{ config('app.name', 'Syamail Muhammad') }}",
  "url": "{{ url('/') }}",
  "potentialAction": {
    "@@type": "SearchAction",
    "target": "{{ url('/search/form') }}?q={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>
@endsection

@section('content')
<article aria-label="Homepage Syamail Muhammad" class="space-y-20">
@php($waAdmin = \App\Support\PhoneUtil::normalize(config('app.contact_phone')))
@php($waText = rawurlencode('Assalamualaikum kak... Saya ingin berlangganan kitab ' . config('app.name') . '. tolong dibantu'))
  <!-- Hero -->
  <section class="relative overflow-hidden">
    <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-br from-emerald-600/10 via-teal-500/10 to-indigo-500/10"></div>
    <div class="max-w-7xl mx-auto px-4 py-16 md:py-24 relative">
      <div class="grid md:grid-cols-2 gap-10 items-center">
        <div>
          <h1 id="hero-title" class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900">
            Syamail Muhammad — Mengenal Pribadi Agung Rasulullah ﷺ
          </h1>
          <p class="mt-4 text-lg text-gray-700" id="hero-subtitle">
            Aplikasi pembaca Syamail karya Imam at‑Tirmidzi: nikmati pengalaman membaca per bab, membuat bookmark, menulis catatan, memantau progres, mendengarkan audio, dan mencari hadits dengan cepat.
          </p>

          <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('chapters.index') }}" id="cta-primary" class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-md" aria-label="Mulai membaca Syamail">
              Mulai Membaca
            </a>
            <a href="{{ route('search.form') }}" id="cta-secondary" class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm" aria-label="Cari hadits di Syamail">
              Cari Hadits
            </a>
          </div>

          <div class="mt-5 flex items-center gap-4 text-sm text-gray-600" aria-label="Indikator kepercayaan">
            <span class="inline-flex items-center gap-1"><svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a6 6 0 00-6 6v2.586l-.707.707A1 1 0 004 13h12a1 1 0 00.707-1.707L16 10.586V8a6 6 0 00-6-6zm-2 9a2 2 0 104 0H8z" clip-rule="evenodd"/></svg> Bebas iklan</span>
            <span class="inline-flex items-center gap-1"><svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2 10a8 8 0 1116 0 8 8 0 01-16 0zm8-5a1 1 0 00-1 1v3.382l-1.447.724a1 1 0 10.894 1.788l2-1A1 1 0 0011 10V6a1 1 0 00-1-1z"/></svg> Akses selamanya</span>
            <span class="inline-flex items-center gap-1"><svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 4a1 1 0 000 2h1v9a2 2 0 002 2h8a2 2 0 002-2V6h1a1 1 0 100-2H3zm5 2a1 1 0 100 2h4a1 1 0 100-2H8z" clip-rule="evenodd"/></svg> Fokus rujukan kitab</span>
          </div>
        </div>

        <!-- Visual cover -->
        <div>
          <figure class="relative rounded-xl border border-gray-200 bg-white p-3 shadow-lg">
            <img src="{{ asset('masjid.jpg') }}"
                 alt="Sampul buku Syamail Muhammad — Imam at‑Tirmidzi"
                 width="640" height="360"
                 class="w-full h-auto rounded-lg object-cover"
                 loading="eager" decoding="async" />
            <figcaption class="sr-only">Sampul Syamail Muhammad karya Imam at‑Tirmidzi</figcaption>
          </figure>
        </div>
      </div>

      <!-- Sticky CTA kecil (desktop) -->
      <div class="hidden md:block fixed right-4 bottom-4 z-40">
        <a href="{{ 'https://api.whatsapp.com/send?phone=' . $waAdmin . '&text=' . $waText }}" id="cta-float" class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-600 text-white shadow-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
          Hubungi Admin
        </a>
      </div>

      <!-- Sticky CTA bar (mobile) -->
      <div class="md:hidden fixed inset-x-0 bottom-0 z-40 bg-white/95 backdrop-blur border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-2">
          <a href="{{ 'https://api.whatsapp.com/send?phone=' . \App\Support\PhoneUtil::normalize(config('app.contact_phone')) . '&text=' . rawurlencode('Assalamualaikum kak... Saya ingin berlangganan kitab ' . config('app.name') . '. tolong dibantu') }}" class="px-7 py-3 rounded-xl bg-white text-emerald-700 font-semibold shadow-lg hover:bg-emerald-50 ring-1 ring-emerald-200" aria-label="Dapatkan akses via WhatsApp admin">Dapatkan Akses</a>
          <a href="{{ route('chapters.index') }}" class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold shadow hover:bg-emerald-700">Mulai Membaca</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Tentang Kitab Syamail -->
  <section aria-labelledby="tentang-title" class="max-w-7xl mx-auto px-4">
    <h2 id="tentang-title" class="text-2xl md:text-3xl font-bold text-gray-900">Tentang Kitab Syamail</h2>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
      <p class="text-gray-700">
        Syamail adalah kumpulan hadits yang menghimpun sifat, akhlak, kebiasaan, dan kehidupan Nabi Muhammad ﷺ. Disusun oleh Imam at‑Tirmidzi, kitab ini membantu kita mengenal Rasulullah ﷺ secara lebih dekat agar cinta dan iman semakin kuat.
      </p>
      <p class="text-gray-700">
        Aplikasi ini memudahkan Anda membaca Syamail secara terstruktur, menyimpan catatan pribadi, menandai hadits penting, melanjutkan bacaan terakhir, dan mencari hadits secara cepat.
      </p>
    </div>
    <p class="mt-3 text-xs text-gray-500">Ringkasan ini bersifat informatif. Rujuk teks kitab untuk referensi utama.</p>
  </section>

  <!-- Tujuan Aplikasi -->
  <section aria-labelledby="tujuan-title" class="max-w-7xl mx-auto px-4">
    <h2 id="tujuan-title" class="text-2xl md:text-3xl font-bold text-gray-900">Tujuan Aplikasi</h2>
    <ul class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
      <li class="rounded-xl border border-gray-200 bg-white p-6">Mendekatkan pembaca kepada Rasulullah ﷺ melalui pemahaman sifat dan akhlak beliau.</li>
      <li class="rounded-xl border border-gray-200 bg-white p-6">Menyediakan pengalaman membaca yang nyaman dan teratur per bab.</li>
      <li class="rounded-xl border border-gray-200 bg-white p-6">Membantu pembaca menyimpan poin penting dengan bookmark dan catatan.</li>
      <li class="rounded-xl border border-gray-200 bg-white p-6">Memantau progres membaca agar istiqamah.</li>
    </ul>
  </section>

  <!-- Fitur Aplikasi -->
  <section aria-labelledby="fitur-title" class="max-w-7xl mx-auto px-4">
    <h2 id="fitur-title" class="text-2xl md:text-3xl font-bold text-gray-900">Fitur Aplikasi</h2>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
        <div class="h-10 w-10 rounded bg-emerald-100 text-emerald-700 grid place-items-center font-bold">1</div>
        <h3 class="mt-4 font-semibold text-gray-900">Baca Per Bab</h3>
        <p class="mt-1 text-gray-600">Navigasi cepat antar bab Syamail.</p>
        <a class="mt-3 inline-block text-emerald-700 hover:text-emerald-800 font-medium" href="{{ route('chapters.index') }}">Lihat Bab</a>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
        <div class="h-10 w-10 rounded bg-emerald-100 text-emerald-700 grid place-items-center font-bold">2</div>
        <h3 class="mt-4 font-semibold text-gray-900">Bookmark</h3>
        <p class="mt-1 text-gray-600">Tandai hadits penting dan kembali dengan mudah.</p>
        <a class="mt-3 inline-block text-emerald-700 hover:text-emerald-800 font-medium" href="{{ route('bookmarks.index') }}">Buka Bookmark</a>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
        <div class="h-10 w-10 rounded bg-emerald-100 text-emerald-700 grid place-items-center font-bold">3</div>
        <h3 class="mt-4 font-semibold text-gray-900">Catatan</h3>
        <p class="mt-1 text-gray-600">Tuliskan renungan dan pelajaran pribadi.</p>
        <a class="mt-3 inline-block text-emerald-700 hover:text-emerald-800 font-medium" href="{{ route('notes.index') }}">Buka Catatan</a>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
        <div class="h-10 w-10 rounded bg-emerald-100 text-emerald-700 grid place-items-center font-bold">4</div>
        <h3 class="mt-4 font-semibold text-gray-900">Progres Membaca</h3>
        <p class="mt-1 text-gray-600">Pantau kemajuan agar istiqamah.</p>
        <a class="mt-3 inline-block text-emerald-700 hover:text-emerald-800 font-medium" href="{{ route('progress.index') }}">Lihat Progres</a>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
        <div class="h-10 w-10 rounded bg-emerald-100 text-emerald-700 grid place-items-center font-bold">5</div>
        <h3 class="mt-4 font-semibold text-gray-900">Audio</h3>
        <p class="mt-1 text-gray-600">Dengarkan audio hadits (bila tersedia) untuk menambah kekhusyukan.</p>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
        <div class="h-10 w-10 rounded bg-emerald-100 text-emerald-700 grid place-items-center font-bold">6</div>
        <h3 class="mt-4 font-semibold text-gray-900">Pencarian</h3>
        <p class="mt-1 text-gray-600">Cari hadits berdasarkan kata kunci.</p>
        <a class="mt-3 inline-block text-emerald-700 hover:text-emerald-800 font-medium" href="{{ route('search.form') }}">Mulai Cari</a>
      </div>
    </div>
  </section>


  <!-- Kutipan Kata Pengantar -->
  <section aria-labelledby="pengantar-title" class="max-w-7xl mx-auto px-4">
    <h2 id="pengantar-title" class="text-2xl md:text-3xl font-bold text-gray-900">Kutipan Kata Pengantar</h2>
    <div class="mt-6 space-y-6">
      <figure class="rounded-xl border border-gray-200 bg-white p-6">
        <blockquote class="text-gray-700">
          “Tiga perkara; jika terdapat pada diri seseorang maka ia akan merasakan manisnya iman (di antaranya) yaitu jika Allah dan Rasul‑Nya lebih ia cintai daripada yang lain.”
        </blockquote>
        <figcaption class="mt-3 text-sm text-gray-500">HR. al‑Bukhari no. 16, 21; Muslim no. 43</figcaption>
      </figure>
      <figure class="rounded-xl border border-gray-200 bg-white p-6">
        <blockquote class="text-gray-700">
          “Nabi itu (hendaknya) lebih utama bagi orang‑orang mukmin dari diri mereka sendiri.”
        </blockquote>
        <figcaption class="mt-3 text-sm text-gray-500">QS. al‑Ahzab: 6</figcaption>
      </figure>
      <figure class="rounded-xl border border-gray-200 bg-white p-6">
        <blockquote class="text-gray-700">
          “Seseorang itu bersama dengan siapa yang dicintainya.”
        </blockquote>
        <figcaption class="mt-3 text-sm text-gray-500">HR. al‑Bukhari no. 3688; riwayat Anas bin Malik</figcaption>
      </figure>
    </div>
    <p class="mt-3 text-xs text-gray-500">Kutipan diringkas dari pengantar cetakan Indonesia. Mohon rujuk teks resmi untuk konteks lengkap.</p>
  </section>

  <!-- Harga Akses -->
  <section aria-labelledby="harga-title" class="max-w-7xl mx-auto px-4">
    <h2 id="harga-title" class="text-2xl md:text-3xl font-bold text-gray-900 text-center">Harga Akses</h2>
    <div class="mt-6 flex justify-center">
      <div class="w-full md:w-[560px] rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-sm">
        <h3 class="text-xl font-semibold text-gray-900">Akses Selamanya</h3>
        <div class="mt-2 text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Rp100.000</div>
        <ul class="mt-4 space-y-2 text-gray-700 text-left mx-auto max-w-sm">
          <li>• Baca seluruh bab Syamail</li>
          <li>• Bookmark, catatan, progres</li>
          <li>• Pembaruan fitur tanpa biaya tambahan</li>
        </ul>        
        <a href="{{ 'https://api.whatsapp.com/send?phone=' . $waAdmin . '&text=' . $waText }}" class="mt-6 inline-flex items-center px-6 py-3 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-md focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" aria-label="Beli akses melalui WhatsApp admin">
          Beli Sekarang
        </a>
        <p class="mt-3 text-xs text-gray-500">Pembayaran via admin WhatsApp. Akses berlaku selamanya untuk satu akun.</p>
      </div>
    </div>
  </section>
  <!-- Petunjuk Memulai -->
  <section aria-labelledby="mulai-title" class="max-w-7xl mx-auto px-4">
    <h2 id="mulai-title" class="text-2xl md:text-3xl font-bold text-gray-900">Petunjuk Memulai</h2>
    <ol class="mt-6 grid md:grid-cols-3 gap-4 list-decimal list-inside">
      <li class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="font-semibold text-gray-900">Buka Bab</h3>
        <p class="text-gray-600 mt-1">Mulai dari bab awal atau pilih bab yang ingin Anda pelajari.</p>
      </li>
      <li class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="font-semibold text-gray-900">Simpan Bookmark</h3>
        <p class="text-gray-600 mt-1">Tandai hadits agar mudah kembali.</p>
      </li>
      <li class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="font-semibold text-gray-900">Tulis Catatan</h3>
        <p class="text-gray-600 mt-1">Catat pelajaran dan renungan pribadi.</p>
      </li>
    </ol>
  </section>

  <!-- Kebijakan singkat -->
  <section class="max-w-4xl mx-auto px-4" id="kebijakan-privasi">
    <h2 class="text-xl font-semibold text-gray-900">Ringkasan Kebijakan</h2>
    <p class="mt-2 text-gray-600">Aplikasi ini tidak menampilkan iklan. Data dasar seperti bookmark dan catatan disimpan dengan kebijakan privasi yang jelas.</p>
  </section>

  <!-- CTA Akhir -->
  <section class="max-w-7xl mx-auto px-4 mt-12 md:mt-16">
    <div class="rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 p-10 md:p-14 text-white text-center shadow-xl">
      <h2 class="text-2xl md:text-3xl font-extrabold">Siap mengenal Rasulullah ﷺ lebih dekat?</h2>
      <p class="mt-2 text-emerald-100">Mulai membaca Syamail secara gratis dan simpan pelajaran Anda.</p>
      <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('chapters.index') }}" class="px-7 py-3 rounded-xl bg-emerald-700 text-white font-semibold shadow-lg hover:bg-emerald-800 ring-1 ring-white/60">Mulai Membaca</a>
        <a href="{{ 'https://api.whatsapp.com/send?phone=' . \App\Support\PhoneUtil::normalize(config('app.contact_phone')) . '&text=' . rawurlencode('Assalamualaikum kak... Saya ingin berlangganan kitab ' . config('app.name') . '. tolong dibantu') }}" class="px-7 py-3 rounded-xl bg-white text-emerald-700 font-semibold shadow-lg hover:bg-emerald-50 ring-1 ring-emerald-200" aria-label="Dapatkan akses via WhatsApp admin">Dapatkan Akses</a>
      </div>
    </div>
  </section>
</article>
@endsection
