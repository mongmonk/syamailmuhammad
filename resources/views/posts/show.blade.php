@extends('layouts.app')

@section('title', $item->caption ?? $item->slug)

@section('meta')
@php
    $thumb = $item->variantUrl('thumb') ?? null;
    $medium = $item->variantUrl('medium') ?? null;
    $large = $item->variantUrl('large') ?? null;
    $max = $item->variantUrl('max') ?? null;
    $primary = $large ?? $max ?? $medium ?? $thumb;

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'ImageObject',
        'name' => $item->caption ?: $item->slug,
        'caption' => $item->caption ?: $item->slug,
        'contentUrl' => $primary,
        'thumbnail' => $thumb ?? $medium ?? $large ?? $max,
    ];
@endphp
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $item->caption ?? $item->slug }}">
<meta property="og:url" content="{{ url()->current() }}">
@if($primary)
<meta property="og:image" content="{{ $primary }}">
@endif
<meta name="twitter:card" content="summary_large_image">
@if($primary)
<meta name="twitter:image" content="{{ $primary }}">
@endif
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <nav class="mb-6">
        <a href="{{ route('posts.index') }}"
           class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600"
           aria-label="Kembali ke Galeri">
            &larr; Kembali ke Galeri
        </a>
    </nav>

    <article class="bg-white border rounded p-6">
        <header>
            <h1 class="text-2xl font-bold">{{ $item->caption ?? $item->slug }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                Dipublikasikan: {{ optional($item->published_at ?? $item->created_at)->format('d M Y H:i') }}
            </p>
        </header>

        @php
            $thumb  = $item->variantUrl('thumb');
            $medium = $item->variantUrl('medium');
            $large  = $item->variantUrl('large');
            $max    = $item->variantUrl('max');
            $src    = $large ?? $max ?? $medium ?? $thumb;
            $srcsetParts = [];
            if ($thumb)  $srcsetParts[] = $thumb.' 320w';
            if ($medium) $srcsetParts[] = $medium.' 640w';
            if ($large)  $srcsetParts[] = $large.' 1280w';
            if ($max)    $srcsetParts[] = $max.' 1920w';
            $srcset = implode(', ', $srcsetParts);
            $sizes  = '100vw';
        @endphp

        <section class="mt-6 leading-relaxed">
            @if($src)
            <div class="relative">
                <img
                    id="detail-image"
                    src="{{ $src }}"
                    @if($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
                    alt="{{ $item->alt_text }}"
                    loading="eager"
                    decoding="async"
                    class="w-full max-h-[75vh] object-contain rounded border bg-gray-100"
                    onerror="this.onerror=null;this.src='{{ asset('icon.jpg') }}';"
                >
                <button id="open-lightbox"
                        class="absolute bottom-3 right-3 px-3 py-1.5 text-xs rounded bg-gray-900/70 text-white hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600"
                        aria-label="Perbesar gambar">Perbesar</button>
            </div>
            @else
            <div class="w-full h-72 bg-gray-100 rounded border flex items-center justify-center text-sm text-gray-500">Gambar tidak tersedia</div>
            @endif

            @if($item->caption)
            <p class="mt-4 text-gray-800">{{ $item->caption }}</p>
            @endif
        </section>
    </article>
</div>

<!-- Lightbox sederhana (tanpa dependency) -->
<div id="lightbox"
     class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50"
     role="dialog" aria-modal="true" aria-labelledby="lightbox-title" aria-hidden="true">
    <div class="max-w-6xl w-full px-4">
        <div class="flex items-center justify-between mb-3">
            <h2 id="lightbox-title" class="text-white text-sm">{{ $item->caption ?? $item->slug }}</h2>
            <div class="space-x-2">
                <a href="{{ $max ?? $large ?? $medium ?? $thumb }}" class="text-xs text-white underline" download aria-label="Unduh gambar">Unduh</a>
                <button id="close-lightbox"
                        class="px-3 py-1.5 text-xs rounded bg-white/20 text-white hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white"
                        aria-label="Tutup lightbox">Tutup</button>
            </div>
        </div>
        <div class="rounded border border-white/20 bg-black/20">
            <img
                id="lightbox-image"
                src="{{ $max ?? $large ?? $medium ?? $thumb }}"
                alt="{{ $item->alt_text }}"
                class="w-full max-h-[85vh] object-contain"
                onerror="this.onerror=null;this.src='{{ asset('icon.jpg') }}';"
            >
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const btnOpen = document.getElementById('open-lightbox');
    const box = document.getElementById('lightbox');
    const btnClose = document.getElementById('close-lightbox');

    if (btnOpen && box) {
        btnOpen.addEventListener('click', function() {
            box.classList.remove('hidden');
            box.setAttribute('aria-hidden', 'false');
            btnClose && btnClose.focus();
        });
    }
    if (btnClose && box) {
        const closeFn = function() {
            box.classList.add('hidden');
            box.setAttribute('aria-hidden', 'true');
            btnOpen && btnOpen.focus();
        };
        btnClose.addEventListener('click', closeFn);
        box.addEventListener('click', function(e) {
            if (e.target === box) closeFn();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeFn();
        });
    }
})();
</script>
@endpush