@extends('layouts.app')

@section('title', 'Admin - Edit Item Galeri')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Edit Item Galeri</h1>

    @if (session('status'))
        <div class="mb-4 p-3 rounded bg-emerald-100 text-emerald-800">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="bg-white border rounded p-4 mb-6">
        <h2 class="text-lg font-semibold mb-3">Pratinjau</h2>
        @php($srcThumb = $item->variantUrl('thumb') ?? $item->variantUrl('medium') ?? $item->variantUrl('large') ?? $item->variantUrl('max'))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <div class="text-xs text-gray-500 mb-1">Thumb (320w)</div>
                @if($item->variantUrl('thumb'))
                    <img src="{{ $item->variantUrl('thumb') }}" alt="{{ $item->alt_text }}" loading="lazy" decoding="async" class="w-full h-32 object-cover rounded border"
                         onerror="this.onerror=null;this.src='{{ asset('icon.jpg') }}'">
                @else
                    <div class="w-full h-32 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-500">Tidak ada</div>
                @endif
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">Medium (640w)</div>
                @if($item->variantUrl('medium'))
                    <img src="{{ $item->variantUrl('medium') }}" alt="{{ $item->alt_text }}" loading="lazy" decoding="async" class="w-full h-32 object-cover rounded border"
                         onerror="this.onerror=null;this.src='{{ asset('icon.jpg') }}'">
                @else
                    <div class="w-full h-32 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-500">Tidak ada</div>
                @endif
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">Large (1280w)</div>
                @if($item->variantUrl('large'))
                    <img src="{{ $item->variantUrl('large') }}" alt="{{ $item->alt_text }}" loading="lazy" decoding="async" class="w-full h-32 object-cover rounded border"
                         onerror="this.onerror=null;this.src='{{ asset('icon.jpg') }}'">
                @else
                    <div class="w-full h-32 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-500">Tidak ada</div>
                @endif
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">Max (1920w)</div>
                @if($item->variantUrl('max'))
                    <img src="{{ $item->variantUrl('max') }}" alt="{{ $item->alt_text }}" loading="lazy" decoding="async" class="w-full h-32 object-cover rounded border"
                         onerror="this.onerror=null;this.src='{{ asset('icon.jpg') }}'">
                @else
                    <div class="w-full h-32 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-500">Tidak ada</div>
                @endif
            </div>
        </div>

        <div class="mt-3 text-xs text-gray-600">
            <div>Slug: <span class="font-mono">{{ $item->slug }}</span></div>
            <div>Nama asli: <span class="break-all">{{ $item->original_filename }}</span> • MIME: {{ $item->mime }}</div>
            <div>Dimensi asli: {{ $item->original_width }}×{{ $item->original_height }} px</div>
        </div>
    </section>

    <form method="POST" action="{{ route('admin.posts.update', $item) }}" class="space-y-4" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div>
            <label for="caption" class="block text-sm text-gray-700">Caption</label>
            <input id="caption" name="caption" type="text" value="{{ old('caption', $item->caption) }}" maxlength="150" class="mt-1 w-full border rounded px-3 py-2" placeholder="Deskripsi singkat gambar (maks. 150 karakter)">
        </div>

        <div>
            <label for="alt_text" class="block text-sm text-gray-700">Alt Text</label>
            <input id="alt_text" name="alt_text" type="text" value="{{ old('alt_text', $item->alt_text) }}" maxlength="180" class="mt-1 w-full border rounded px-3 py-2" placeholder="Teks alternatif untuk aksesibilitas">
            <p class="text-xs text-gray-500 mt-1">Jika kosong, akan menggunakan caption sebagai alt text.</p>
        </div>

        <div>
            <label for="tags" class="block text-sm text-gray-700">Tags</label>
            <input id="tags" name="tags" type="text" value="{{ old('tags', is_array($item->tags) ? implode(', ', $item->tags) : '') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="Pisahkan dengan koma, mis. kajian, muhammadiyah">
        </div>

        <div class="flex items-center space-x-2">
            <input type="hidden" name="is_active" value="0">
            <input id="is_active" name="is_active" type="checkbox" value="1" @if(old('is_active', $item->is_active ? '1' : '')==='1') checked @endif class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
            <label for="is_active" class="text-sm text-gray-700">Aktifkan & publikasikan</label>
        </div>

        <div class="border rounded p-4 bg-white">
            <label for="image" class="block text-sm text-gray-700">Ganti Gambar (opsional)</label>
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="mt-1 w-full border rounded px-3 py-2">
            <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG/WebP, maksimal 5 MB. Mengunggah ulang akan memproses ulang semua varian dan mengganti file.</p>

            <div id="preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-3"></div>
        </div>

        <div class="flex items-center space-x-3">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Simpan Perubahan</button>
            <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Kembali</a>
        </div>
    </form>

    <div class="flex items-center justify-end mt-6">
        <form method="POST" action="{{ route('admin.posts.destroy', $item) }}" onsubmit="return confirm('Hapus item ini dan semua varian gambar di storage? Tindakan tidak dapat dibatalkan.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 border rounded text-red-700 border-red-300 hover:bg-red-50">Hapus</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const input = document.getElementById('image');
    const preview = document.getElementById('preview');
    if (!input || !preview) return;

    input.addEventListener('change', function() {
        while (preview.firstChild) preview.removeChild(preview.firstChild);
        const file = input.files && input.files[0] ? input.files[0] : null;
        if (!file) return;
        const typeOk = ['image/jpeg','image/png','image/webp'].includes(file.type);
        const sizeOk = file.size <= (5 * 1024 * 1024);

        const wrap = document.createElement('div');
        wrap.className = (typeOk && sizeOk) ? 'border rounded bg-white p-2' : 'border rounded bg-red-50 p-2';

        const info = document.createElement('div');
        info.className = 'text-xs text-gray-600 mt-2 break-all';
        info.textContent = (file.name || 'file') + ' • ' + Math.round(file.size/1024) + ' KB';

        if (!typeOk || !sizeOk) {
            const warn = document.createElement('div');
            warn.className = 'text-xs text-red-700';
            warn.textContent = !typeOk ? 'Format tidak didukung' : 'Ukuran melebihi 5 MB';
            wrap.appendChild(warn);
        }

        if (typeOk && sizeOk) {
            const img = document.createElement('img');
            img.alt = file.name || 'preview';
            img.loading = 'lazy';
            img.decoding = 'async';
            img.className = 'w-full h-24 object-cover rounded';
            img.onerror = function() {
                img.onerror = null;
                img.src = '{{ asset('icon.jpg') }}';
                img.classList.add('bg-gray-100');
            };
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
            wrap.appendChild(img);
        }
        wrap.appendChild(info);
        preview.appendChild(wrap);
    });
})();
</script>
@endpush