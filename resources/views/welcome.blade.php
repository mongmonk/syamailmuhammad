@extends('layouts.app')

@section('title', 'Syamail Teams — Produktivitas Tim Lebih Cepat, Rapat Lebih Singkat, Hasil Lebih Terukur | Coba Gratis')

@section('meta')
<meta name="description" content="Syamail Teams membantu tim Anda fokus pada hasil: kerja 30% lebih cepat, rapat 2x lebih singkat, kolaborasi lebih rapi. Coba gratis tanpa kartu kredit.">
<meta property="og:type" content="website">
<meta property="og:title" content="Syamail Teams — Produktivitas Tim Lebih Cepat, Rapat Lebih Singkat">
<meta property="og:description" content="Fokus pada output, bukan remeh-temeh. Coba gratis — tanpa kartu kredit.">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ asset('favicon.ico') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Syamail Teams — Produktivitas Tim Lebih Cepat">
<meta name="twitter:description" content="Kerja 30% lebih cepat, rapat 2x lebih singkat. Coba gratis.">
<meta name="twitter:image" content="{{ asset('favicon.ico') }}">
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Organization",
  "name": "{{ config('app.name', 'Syamail Teams') }}",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('favicon.ico') }}",
  "contactPoint": [{
    "@@type": "ContactPoint",
    "contactType": "sales",
    "email": "sales@example.com",
    "areaServed": "ID",
    "availableLanguage": ["id", "en"]
  }]
}
</script>
<script type="application/ld+json">
{
 "@@context": "https://schema.org",
 "@@type": "FAQPage",
 "mainEntity": [
   {
     "@@type": "Question",
     "name": "Apakah benar-benar gratis untuk memulai?",
     "acceptedAnswer": {
       "@@type": "Answer",
       "text": "Ya. Anda dapat mencoba gratis tanpa kartu kredit. Paket berbayar tersedia saat tim Anda siap skala."
     }
   },
   {
     "@@type": "Question",
     "name": "Bagaimana keamanan data saya?",
     "acceptedAnswer": {
       "@@type": "Answer",
       "text": "Data dienkripsi saat transit dan saat tersimpan. Kami menerapkan kontrol akses ketat dan audit log."
     }
   },
   {
     "@@type": "Question",
     "name": "Apakah bisa terintegrasi dengan alur kerja saya?",
     "acceptedAnswer": {
       "@@type": "Answer",
       "text": "Ya. Kami menyediakan integrasi via API, impor/ekspor data, dan SSO untuk perusahaan."
     }
   }
 ]
}
</script>
@endsection

@section('content')
<article aria-label="Landing Page Syamail Teams" class="space-y-20">
  <!-- Hero -->
  <section class="relative overflow-hidden">
    <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-br from-indigo-600/10 via-purple-500/10 to-blue-500/10"></div>
    <div class="max-w-7xl mx-auto px-4 py-16 md:py-24 relative">
      <div class="grid md:grid-cols-2 gap-10 items-center">
        <div>
          <h1 id="hero-title" class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900">
            Produktivitas tim meroket, rapat lebih singkat, hasil lebih cepat
          </h1>
          <p class="mt-4 text-lg text-gray-600" id="hero-subtitle">
            Pangkas kekacauan, selaraskan prioritas, dan dorong eksekusi. Rata-rata tim melaporkan proses lebih rapi dan keputusan lebih cepat setelah 14 hari.
          </p>

          <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a href="#coba-gratis" id="cta-primary" class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition shadow-md"
               aria-label="Coba gratis sekarang">
              Coba Gratis
            </a>
            <a href="#demo" id="cta-secondary" class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition shadow-sm"
               aria-label="Lihat demo produk">
              Lihat Demo
            </a>
          </div>

          <div class="mt-5 flex items-center gap-4 text-sm text-gray-600" aria-label="Indikator kepercayaan">
            <span class="inline-flex items-center gap-1"><svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a6 6 0 00-6 6v2.586l-.707.707A1 1 0 004 13h12a1 1 0 00.707-1.707L16 10.586V8a6 6 0 00-6-6zm-2 9a2 2 0 104 0H8z" clip-rule="evenodd"/></svg> Keamanan setara enterprise</span>
            <span class="inline-flex items-center gap-1"><svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2 10a8 8 0 1116 0 8 8 0 01-16 0zm8-5a1 1 0 00-1 1v3.382l-1.447.724a1 1 0 10.894 1.788l2-1A1 1 0 0011 10V6a1 1 0 00-1-1z"/></svg> Hemat waktu meeting</span>
            <span class="inline-flex items-center gap-1"><svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 4a1 1 0 000 2h1v9a2 2 0 002 2h8a2 2 0 002-2V6h1a1 1 0 100-2H3zm5 2a1 1 0 100 2h4a1 1 0 100-2H8z" clip-rule="evenodd"/></svg> Tanpa kartu kredit</span>
          </div>
        </div>

        <!-- Visual produk (placeholder aksesibel, tanpa gambar berat) -->
        <div aria-hidden="true">
          <div class="relative rounded-xl border border-gray-200 bg-white/60 backdrop-blur p-4 shadow-lg">
            <div class="h-8 w-32 bg-gradient-to-r from-indigo-500 to-purple-500 rounded"></div>
            <div class="mt-4 grid grid-cols-3 gap-3">
              <div class="col-span-2 h-36 rounded-lg bg-gray-100"></div>
              <div class="col-span-1 space-y-3">
                <div class="h-10 rounded bg-gray-100"></div>
                <div class="h-10 rounded bg-gray-100"></div>
                <div class="h-10 rounded bg-gray-100"></div>
              </div>
            </div>
            <div class="mt-4 h-10 rounded bg-indigo-50"></div>
          </div>
          <p class="sr-only">{{ config('app.hero_image_desc') }}</p>
        </div>
      </div>

      <!-- Sticky CTA kecil selalu terlihat (desktop) -->
      <div class="hidden md:block fixed right-4 bottom-4 z-40">
        <a href="#coba-gratis" id="cta-float" class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-600 text-white shadow-lg hover:bg-emerald-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
          Mulai Coba Gratis
        </a>
      </div>

      <!-- Sticky CTA bar (mobile) -->
      <div class="md:hidden fixed inset-x-0 bottom-0 z-40 bg-white/95 backdrop-blur border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-2">
          <a href="#demo" class="px-4 py-2 rounded-lg bg-indigo-50 text-indigo-700 font-medium">Lihat Demo</a>
          <a href="#coba-gratis" class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold shadow hover:bg-emerald-700">Coba Gratis</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Ringkasan manfaat (konkret dan terukur) -->
  <section aria-labelledby="manfaat-title" class="max-w-7xl mx-auto px-4">
    <h2 id="manfaat-title" class="text-2xl md:text-3xl font-bold text-gray-900">Hasil yang berarti, bukan sekadar aktivitas</h2>
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:shadow-md">
        <div class="text-3xl font-extrabold text-indigo-600"><span data-count-to="30">0</span>%</div>
        <p class="mt-2 text-gray-700">Waktu eksekusi lebih cepat berkat alur kerja yang ringkas.</p>
      </div>
      <div class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:shadow-md">
        <div class="text-3xl font-extrabold text-indigo-600"><span data-count-to="2">0</span>x</div>
        <p class="mt-2 text-gray-700">Rapat lebih singkat dengan agenda jelas dan ringkasan otomatis.</p>
      </div>
      <div class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:shadow-md">
        <div class="text-3xl font-extrabold text-indigo-600"><span data-count-to="1">0</span> tempat</div>
        <p class="mt-2 text-gray-700">Semua keputusan terdokumentasi rapi dalam satu tempat.</p>
      </div>
    </div>
    <p class="mt-3 text-xs text-gray-500">Estimasi berbasis simulasi internal; hasil dapat bervariasi tergantung konteks tim.</p>
  </section>

  <!-- Fitur inti -->
  <section aria-labelledby="fitur-title" class="max-w-7xl mx-auto px-4">
    <h2 id="fitur-title" class="text-2xl md:text-3xl font-bold text-gray-900">Fitur inti yang fokus pada output</h2>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
        <div class="h-10 w-10 rounded bg-indigo-100 text-indigo-700 grid place-items-center font-bold">1</div>
        <h3 class="mt-4 font-semibold text-gray-900">Prioritas Harian</h3>
        <p class="mt-1 text-gray-600">Susun fokus harian yang nyata, bukan sekadar daftar panjang.</p>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
        <div class="h-10 w-10 rounded bg-indigo-100 text-indigo-700 grid place-items-center font-bold">2</div>
        <h3 class="mt-4 font-semibold text-gray-900">Ringkasan Rapat</h3>
        <p class="mt-1 text-gray-600">Agenda jelas, keputusan tercatat, tindak lanjut otomatis.</p>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
        <div class="h-10 w-10 rounded bg-indigo-100 text-indigo-700 grid place-items-center font-bold">3</div>
        <h3 class="mt-4 font-semibold text-gray-900">Dokumentasi Keputusan</h3>
        <p class="mt-1 text-gray-600">Satu sumber kebenaran untuk semua keputusan kritikal.</p>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-6 hover:shadow-md transition">
        <div class="h-10 w-10 rounded bg-indigo-100 text-indigo-700 grid place-items-center font-bold">4</div>
        <h3 class="mt-4 font-semibold text-gray-900">Analitik Ringkas</h3>
        <p class="mt-1 text-gray-600">Lihat tren eksekusi dan bottleneck tanpa dashboard rumit.</p>
      </div>
    </div>
  </section>

  <!-- Social proof -->
  <section aria-labelledby="social-proof-title" class="max-w-7xl mx-auto px-4">
    <h2 id="social-proof-title" class="text-2xl md:text-3xl font-bold text-gray-900">Dipercaya tim yang mengejar hasil</h2>
    <div class="mt-6 grid md:grid-cols-3 gap-4">
      <figure class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="flex items-center gap-2" aria-label="Rating bintang 5 dari 5">
          <span class="sr-only">Rating: 5/5</span>
          <div class="flex text-amber-500" aria-hidden="true">
            <span>★★★★★</span>
          </div>
        </div>
        <blockquote class="mt-3 text-gray-700">“Rapat kami turun separuh durasi, tapi hasilnya 2x lebih jelas.”</blockquote>
        <figcaption class="mt-3 text-sm text-gray-500">Dina — PM, Tech Startup</figcaption>
      </figure>
      <figure class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="flex items-center gap-2">
          <span class="sr-only">Rating: 5/5</span>
          <div class="flex text-amber-500" aria-hidden="true"><span>★★★★★</span></div>
        </div>
        <blockquote class="mt-3 text-gray-700">“Keputusan terdokumentasi membuat onboarding anggota baru jauh lebih cepat.”</blockquote>
        <figcaption class="mt-3 text-sm text-gray-500">Arif — Lead Engineer</figcaption>
      </figure>
      <figure class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="flex items-center gap-2">
          <span class="sr-only">Rating: 5/5</span>
          <div class="flex text-amber-500" aria-hidden="true"><span>★★★★★</span></div>
        </div>
        <blockquote class="mt-3 text-gray-700">“Akhirnya alat yang mendorong eksekusi, bukan menambah pekerjaan.”</blockquote>
        <figcaption class="mt-3 text-sm text-gray-500">Lala — Head of Ops</figcaption>
      </figure>
    </div>
    <div class="mt-8 grid grid-cols-2 sm:grid-cols-5 gap-3" aria-label="Logo klien">
      <div class="h-10 rounded bg-gray-100" role="img" aria-label="Logo Klien 1"></div>
      <div class="h-10 rounded bg-gray-100" role="img" aria-label="Logo Klien 2"></div>
      <div class="h-10 rounded bg-gray-100" role="img" aria-label="Logo Klien 3"></div>
      <div class="h-10 rounded bg-gray-100" role="img" aria-label="Logo Klien 4"></div>
      <div class="h-10 rounded bg-gray-100" role="img" aria-label="Logo Klien 5"></div>
    </div>
  </section>

  <!-- Cara kerja -->
  <section aria-labelledby="cara-kerja-title" class="max-w-7xl mx-auto px-4">
    <h2 id="cara-kerja-title" class="text-2xl md:text-3xl font-bold text-gray-900">Cara kerja dalam 3 langkah</h2>
    <ol class="mt-6 grid md:grid-cols-3 gap-4 list-decimal list-inside">
      <li class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="font-semibold text-gray-900">Tetapkan fokus</h3>
        <p class="text-gray-600 mt-1">Tentukan 3 prioritas harian/pekan. Semua orang selaras.</p>
      </li>
      <li class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="font-semibold text-gray-900">Jalankan rapat efektif</h3>
        <p class="text-gray-600 mt-1">Agenda ringkas, waktu ketat, keputusan dicatat otomatis.</p>
      </li>
      <li class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="font-semibold text-gray-900">Kunci keputusan & tindak lanjut</h3>
        <p class="text-gray-600 mt-1">Semua keputusan dan aksi mudah dilacak hingga tuntas.</p>
      </li>
    </ol>
  </section>

  <!-- Use case -->
  <section aria-labelledby="usecase-title" class="max-w-7xl mx-auto px-4">
    <h2 id="usecase-title" class="text-2xl md:text-3xl font-bold text-gray-900">Cocok untuk tim yang ingin bergerak cepat</h2>
    <div class="mt-6 grid md:grid-cols-3 gap-4">
      <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="font-semibold text-gray-900">Produk & Engineering</h3>
        <p class="text-gray-600 mt-1">Roadmap jelas, prioritas tajam, handoff tanpa drama.</p>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="font-semibold text-gray-900">Operasional</h3>
        <p class="text-gray-600 mt-1">SOP hidup, eskalasi rapi, SLA terpantau.</p>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="font-semibold text-gray-900">Penjualan</h3>
        <p class="text-gray-600 mt-1">Pipeline bersih, follow-up tepat, closing meningkat.</p>
      </div>
    </div>
  </section>

  <!-- Demo -->
  <section id="demo" aria-labelledby="demo-title" class="max-w-7xl mx-auto px-4">
    <h2 id="demo-title" class="text-2xl md:text-3xl font-bold text-gray-900">Lihat demo dalam 90 detik</h2>
    <div class="mt-6 aspect-video rounded-xl overflow-hidden border border-gray-200 bg-gray-50 grid place-items-center">
      <iframe class="w-full h-full" src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ" title="Demo Syamail Teams" loading="lazy" allowfullscreen></iframe>
    </div>
  </section>

  <!-- Harga / Hubungi Sales -->
  <section aria-labelledby="harga-title" class="max-w-7xl mx-auto px-4">
    <h2 id="harga-title" class="text-2xl md:text-3xl font-bold text-gray-900">Mulai gratis — skala saat siap</h2>
    <div class="mt-6 grid md:grid-cols-2 gap-4">
      <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <h3 class="text-xl font-semibold text-gray-900">Starter</h3>
        <p class="mt-1 text-gray-600">Untuk tim kecil yang ingin bergerak cepat.</p>
        <div class="mt-4 text-3xl font-extrabold text-gray-900">Rp0<span class="text-base font-medium text-gray-600">/tim</span></div>
        <ul class="mt-4 space-y-2 text-gray-700">
          <li>• Prioritas & agenda rapat</li>
          <li>• Dokumentasi keputusan</li>
          <li>• Integrasi dasar</li>
        </ul>
        <a href="#coba-gratis" class="mt-6 inline-flex items-center px-5 py-3 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">Coba Gratis</a>
      </div>
      <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-indigo-50 to-purple-50 p-6">
        <h3 class="text-xl font-semibold text-gray-900">Perusahaan</h3>
        <p class="mt-1 text-gray-600">S&K perusahaan, SSO, audit lanjutan, dan dukungan prioritas.</p>
        <ul class="mt-4 space-y-2 text-gray-700">
          <li>• SSO & kontrol akses granular</li>
          <li>• Audit log & kebijakan retensi</li>
          <li>• Integrasi kustom & SLA</li>
        </ul>
        <a href="#hubungi-sales" class="mt-6 inline-flex items-center px-5 py-3 rounded-lg bg-indigo-700 text-white hover:bg-indigo-800 transition">Bicara dengan Tim Penjualan</a>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section aria-labelledby="faq-title" class="max-w-7xl mx-auto px-4">
    <h2 id="faq-title" class="text-2xl md:text-3xl font-bold text-gray-900">Pertanyaan yang sering diajukan</h2>
    <div class="mt-6 space-y-3">
      <details class="group rounded-xl border border-gray-200 bg-white p-5 open:shadow-md">
        <summary class="cursor-pointer list-none flex items-center justify-between">
          <span class="font-semibold text-gray-900">Bagaimana proses onboarding?</span>
          <span aria-hidden="true" class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="mt-2 text-gray-700">Undang tim, impor prioritas, mulai rapat efektif pertama Anda dalam hitungan menit.</p>
      </details>
      <details class="group rounded-xl border border-gray-200 bg-white p-5 open:shadow-md">
        <summary class="cursor-pointer list-none flex items-center justify-between">
          <span class="font-semibold text-gray-900">Apakah ada kontrak jangka panjang?</span>
          <span aria-hidden="true" class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="mt-2 text-gray-700">Tidak. Mulai gratis, upgrade kapan pun sesuai kebutuhan Anda.</p>
      </details>
      <details class="group rounded-xl border border-gray-200 bg-white p-5 open:shadow-md">
        <summary class="cursor-pointer list-none flex items-center justify-between">
          <span class="font-semibold text-gray-900">Apakah mendukung SSO?</span>
          <span aria-hidden="true" class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="mt-2 text-gray-700">Ya, paket perusahaan mendukung SSO dan kebijakan keamanan lanjutan.</p>
      </details>
    </div>
  </section>

  <!-- Lead form -->
  <section id="coba-gratis" aria-labelledby="lead-title" class="max-w-4xl mx-auto px-4">
    <h2 id="lead-title" class="text-2xl md:text-3xl font-bold text-gray-900">Dapatkan akses — gratis 14 hari</h2>
    <form id="lead-form" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4" novalidate>
      <div class="md:col-span-1">
        <label for="lead-name" class="block text-sm font-medium text-gray-700">Nama</label>
        <input id="lead-name" name="name" type="text" autocomplete="name" required class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nama Anda">
        <p class="mt-1 text-xs text-red-600 hidden" data-error-for="lead-name">Nama wajib diisi.</p>
      </div>
      <div class="md:col-span-1">
        <label for="lead-email" class="block text-sm font-medium text-gray-700">Email Kerja</label>
        <input id="lead-email" name="email" type="email" autocomplete="email" required class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="nama@perusahaan.com">
        <p class="mt-1 text-xs text-red-600 hidden" data-error-for="lead-email">Email valid wajib diisi.</p>
      </div>
      <div class="md:col-span-1">
        <label for="lead-company" class="block text-sm font-medium text-gray-700">Perusahaan</label>
        <input id="lead-company" name="company" type="text" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nama perusahaan">
      </div>
      <div class="md:col-span-1">
        <label for="lead-phone" class="block text-sm font-medium text-gray-700">Nomor Telepon (opsional)</label>
        <input id="lead-phone" name="phone" type="tel" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="+62 ...">
      </div>

      <!-- Honeypot -->
      <div class="hidden">
        <label>Website</label>
        <input type="text" name="website" tabindex="-1" autocomplete="off">
      </div>

      <div class="md:col-span-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <button type="submit" id="cta-submit" class="inline-flex items-center px-6 py-3 rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition shadow-md">
          Buat Akun Trial
        </button>
        <div class="text-xs text-gray-600 flex items-center gap-2">
          <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a6 6 0 00-6 6v2.586l-.707.707A1 1 0 004 13h12a1 1 0 00.707-1.707L16 10.586V8a6 6 0 00-6-6zm-2 9a2 2 0 104 0H8z" clip-rule="evenodd"/></svg>
          Data dienkripsi. <a href="#kebijakan-privasi" class="text-indigo-700 underline hover:no-underline">Kebijakan Privasi</a> & jaminan uang kembali 14 hari.
        </div>
      </div>
    </form>
    <div id="lead-success" class="mt-3 hidden rounded-md bg-emerald-50 text-emerald-800 px-4 py-3">Terima kasih! Kami telah mengirimkan tautan aktivasi ke email Anda.</div>
  </section>

  <!-- Kebijakan sederhana (anchor untuk link) -->
  <section id="kebijakan-privasi" class="max-w-4xl mx-auto px-4">
    <h2 class="text-xl font-semibold text-gray-900">Ringkasan Kebijakan & Keamanan</h2>
    <p class="mt-2 text-gray-600">Kami menerapkan enkripsi in-transit & at-rest, kontrol akses berbasis peran, serta audit log untuk aktivitas penting.</p>
  </section>

  <!-- CTA Akhir -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white text-center">
      <h2 class="text-2xl md:text-3xl font-extrabold">Siap bekerja lebih fokus dan cepat?</h2>
      <p class="mt-2 text-indigo-100">Mulai gratis sekarang. Butuh demo? Lihat 90 detik ringkas sebelum memutuskan.</p>
      <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
        <a href="#coba-gratis" class="px-6 py-3 rounded-lg bg-white text-indigo-700 font-semibold hover:bg-indigo-50 transition">Coba Gratis</a>
        <a href="#demo" class="px-6 py-3 rounded-lg bg-white/10 text-white border border-white/30 hover:bg-white/20 transition">Lihat Demo</a>
      </div>
    </div>
  </section>
</article>

@push('scripts')
<script>
(function() {
  // DataLayer untuk pelacakan sederhana
  window.dataLayer = window.dataLayer || [];

  // A/B Variants
  var heroTitles = [
    "Produktivitas tim meroket, rapat lebih singkat, hasil lebih cepat",
    "Fokus pada hasil — kerja 30% lebih cepat, rapat 2x lebih efektif"
  ];
  var ctaPrimaryVariants = [
    "Coba Gratis",
    "Mulai Uji Coba"
  ];

  function pickVariant(key, arr) {
    try {
      var stored = sessionStorage.getItem(key);
      if (stored !== null) { stored = parseInt(stored, 10); if (!isNaN(stored)) return stored; }
    } catch(e) {}
    var idx = Math.floor(Math.random() * arr.length);
    try { sessionStorage.setItem(key, idx); } catch(e) {}
    return idx;
  }

  var titleIdx = pickVariant("ab_hero_title", heroTitles);
  var ctaIdx = pickVariant("ab_cta_primary", ctaPrimaryVariants);

  var heroTitleEl = document.getElementById("hero-title");
  if (heroTitleEl) heroTitleEl.textContent = heroTitles[titleIdx];

  var ctaPrimaryEl = document.getElementById("cta-primary");
  if (ctaPrimaryEl) ctaPrimaryEl.textContent = ctaPrimaryVariants[ctaIdx];

  // Tracking helper
  function track(eventName, payload) {
    try {
      window.dataLayer.push(Object.assign({ event: eventName, page: 'home', ts: Date.now() }, payload || {}));
    } catch(e) {}
  }

  // CTA clicks
  ["cta-primary", "cta-secondary", "cta-float", "cta-submit"].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) {
      el.addEventListener("click", function() {
        track("cta_click", { cta_id: id, variant_title: titleIdx, variant_cta: ctaIdx, text: el.textContent.trim() });
      });
    }
  });

  // Animated counters
  var counters = document.querySelectorAll("[data-count-to]");
  if (counters.length) {
    var obs = new IntersectionObserver(function(entries, observer) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var to = parseFloat(el.getAttribute("data-count-to"));
          var duration = 1200;
          var start = null;
          function step(ts) {
            if (!start) start = ts;
            var p = Math.min(1, (ts - start) / duration);
            el.textContent = Math.floor(p * to);
            if (p < 1) requestAnimationFrame(step);
          }
          requestAnimationFrame(step);
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.5 });
    counters.forEach(function(el){ obs.observe(el); });
  }

  // Lead form validation + submit
  var form = document.getElementById("lead-form");
  if (form) {
    form.addEventListener("submit", function(e) {
      e.preventDefault();
      var name = document.getElementById("lead-name");
      var email = document.getElementById("lead-email");
      var website = form.querySelector('input[name="website"]');
      var ok = true;

      function showErr(input, okCond, msgKey) {
        var err = document.querySelector('[data-error-for="'+ input.id +'"]');
        if (!okCond) {
          ok = false;
          if (err) err.classList.remove("hidden");
          input.setAttribute("aria-invalid", "true");
        } else {
          if (err) err.classList.add("hidden");
          input.removeAttribute("aria-invalid");
        }
      }

      showErr(name, !!name.value.trim(), "lead-name");
      var emailValid = !!email.value.trim() && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim());
      showErr(email, emailValid, "lead-email");

      // Honeypot check
      if (website && website.value) {
        ok = false; // bot detected
      }

      if (!ok) {
        track("lead_submit_error", { reason: "validation", fields: { name: !!name.value.trim(), email: emailValid }});
        return;
      }

      // Simulasi submit sukses (integrasi backend bisa ditambahkan kemudian)
      track("lead_submit", { name: name.value.trim(), email_domain: email.value.trim().split("@").pop(), variant_title: titleIdx, variant_cta: ctaIdx });

      var success = document.getElementById("lead-success");
      if (success) success.classList.remove("hidden");
      form.reset();
    });
  }
})();
</script>
@endpush
@endsection
