@extends('layouts.app')

@section('title', 'Galeri')

@section('meta')
@php
    $first = isset($items) && $items->count() > 0 ? $items->first() : null;
    $firstImg = $first ? ($first->variantUrl('large') ?? $first->variantUrl('max') ?? $first->variantUrl('medium') ?? $first->variantUrl('thumb')) : null;

    // Siapkan Schema.org ImageObject untuk halaman ini (dibatasi sesuai item pada halaman saat ini)
    $imageObjects = [];
    if (isset($items)) {
        foreach ($items as $it) {
            $thumb = $it->variantUrl('thumb') ?? $it->variantUrl('medium') ?? $it->variantUrl('large') ?? $it->variantUrl('max');
            $content = $it->variantUrl('large') ?? $it->variantUrl('max') ?? $thumb;
            if ($content) {
                $imageObjects[] = [
                    '@type' => 'ImageObject',
                    'contentUrl' => $content,
                    'thumbnail' => $thumb,
                    'name' => $it->caption ?: $it->slug,
                    'caption' => $it->caption ?: $it->slug,
                ];
            }
        }
    }
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => 'Galeri',
        'hasPart' => $imageObjects,
    ];
@endphp
<meta property="og:type" content="website">
<meta property="og:title" content="Galeri">
<meta property="og:url" content="{{ url()->current() }}">
@if($firstImg)
<meta property="og:image" content="{{ $firstImg }}">
@endif
<meta name="twitter:card" content="summary_large_image">
@if($firstImg)
<meta name="twitter:image" content="{{ $firstImg }}">
@endif
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Galeri</h1>

    <form method="GET" action="{{ route('posts.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-6 gap-3" role="search" aria-label="Pencarian galeri">
        <div class="md:col-span-4">
            <label for="q" class="block text-sm text-gray-700">Cari Caption</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="kata kunci" autocomplete="off">
        </div>
        <div class="md:col-span-1">
            <label for="tag" class="block text-sm text-gray-700">Tag</label>
            <input type="text" id="tag" name="tag" value="{{ request('tag') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="mis. kajian" autocomplete="off">
        </div>
        <div class="md:col-span-1 flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">Cari</button>
        </div>
    </form>

    @if (!isset($items) || $items->count() === 0)
        <div class="bg-white border rounded p-6 text-center text-gray-600">
            Belum ada gambar yang tersedia.
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($items as $it)
                @php
                    $thumb = $it->variantUrl('thumb');
                    $medium = $it->variantUrl('medium');
                    $large = $it->variantUrl('large');
                    $max = $it->variantUrl('max');
                    $src = $medium ?? $thumb ?? $large ?? $max;
                    $srcsetParts = [];
                    if ($thumb)  $srcsetParts[] = $thumb.' 320w';
                    if ($medium) $srcsetParts[] = $medium.' 640w';
                    if ($large)  $srcsetParts[] = $large.' 1280w';
                    if ($max)    $srcsetParts[] = $max.' 1920w';
                    $srcset = implode(', ', $srcsetParts);
                    $sizes = '(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw';
                @endphp
                <figure class="group">
                    <a href="{{ route('posts.show', $it->slug) }}"
                       class="block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 rounded"
                       aria-label="Lihat gambar: {{ $it->caption ?: $it->slug }}">
                        @if($src)
                        <img
                            src="{{ $src }}"
                            @if($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
                            alt="{{ $it->alt_text }}"
                            loading="lazy"
                            decoding="async"
                            class="w-full h-48 object-cover rounded border bg-gray-100 group-hover:opacity-95 transition-opacity"
                            onerror="this.onerror=null;this.src='{{ asset('icon.jpg') }}';"
                        >
                        @else
                        <div class="w-full h-48 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-500">Tidak ada gambar</div>
                        @endif
                    </a>
                    <figcaption class="mt-2 text-sm text-gray-800">{{ $it->caption ?? '—' }}</figcaption>
                </figure>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection