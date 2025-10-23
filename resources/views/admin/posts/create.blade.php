@extends('layouts.app')

@section('title', 'Admin - Unggah Gambar Galeri')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Unggah Gambar Galeri</h1>

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

    <div class="mb-4 p-3 rounded bg-blue-50 text-blue-800 text-sm">
        <ul class="list-disc list-inside">
            <li>Format diperbolehkan: JPG, PNG, atau WebP</li>
            <li>Maksimal ukuran per file: 5 MB</li>
            <li>Anda dapat memilih banyak file sekaligus</li>
            <li>Caption maksimal 150 karakter, Alt text default sama dengan caption</li>
        </ul>
    </div>

    <form method="POST" action="{{ route('admin.posts.store') }}" class="space-y-4" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="images" class="block text-sm text-gray-700">Pilih Gambar</label>
            <input id="images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple required class="mt-1 w-full border rounded px-3 py-2">
            <p class="text-xs text-gray-500 mt-1">Anda dapat memilih beberapa gambar sekaligus.</p>
        </div>

        <div>
            <label for="caption" class="block text-sm text-gray-700">Caption (opsional)</label>
            <input id="caption" name="caption" type="text" value="{{ old('caption') }}" maxlength="150" class="mt-1 w-full border rounded px-3 py-2" placeholder="Deskripsi singkat gambar (maks. 150 karakter)">
        </div>

        <div>
            <label for="alt_text" class="block text-sm text-gray-700">Alt Text (opsional)</label>
            <input id="alt_text" name="alt_text" type="text" value="{{ old('alt_text') }}" maxlength="180" class="mt-1 w-full border rounded px-3 py-2" placeholder="Teks alternatif untuk aksesibilitas">
            <p class="text-xs text-gray-500 mt-1">Jika kosong, akan menggunakan caption sebagai alt text.</p>
        </div>

        <div>
            <label for="tags" class="block text-sm text-gray-700">Tags (opsional)</label>
            <input id="tags" name="tags" type="text" value="{{ old('tags') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="Pisahkan dengan koma, mis. kajian, muhammadiyah">
        </div>

        <div class="flex items-center space-x-2">
            <input type="hidden" name="is_active" value="0">
            <input id="is_active" name="is_active" type="checkbox" value="1" @if(old('is_active', '1')==='1') checked @endif class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
            <label for="is_active" class="text-sm text-gray-700">Aktifkan & publikasikan</label>
        </div>

        <div id="preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-4"></div>

        <div class="flex items-center space-x-3">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Unggah</button>
            <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const input = document.getElementById('images');
    const preview = document.getElementById('preview');
    if (!input || !preview) return;

    input.addEventListener('change', function() {
        while (preview.firstChild) preview.removeChild(preview.firstChild);
        const files = Array.from(input.files || []);
        files.forEach(function(file) {
            const typeOk = ['image/jpeg','image/png','image/webp'].includes(file.type);
            const sizeOk = file.size <= (5 * 1024 * 1024);
            const item = document.createElement('div');
            item.className = 'border rounded bg-white p-2';
            const info = document.createElement('div');
            info.className = 'text-xs text-gray-600 mt-2 break-all';
            info.textContent = (file.name || 'file') + ' • ' + Math.round(file.size/1024) + ' KB';
            if (!typeOk || !sizeOk) {
                item.className = 'border rounded bg-red-50 p-2';
                const warn = document.createElement('div');
                warn.className = 'text-xs text-red-700';
                warn.textContent = !typeOk ? 'Format tidak didukung' : 'Ukuran melebihi 5 MB';
                item.appendChild(warn);
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
                item.appendChild(img);
            }
            item.appendChild(info);
            preview.appendChild(item);
        });
    });
})();
</script>
@endpush