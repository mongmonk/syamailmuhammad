@extends('layouts.app')

@section('title', 'Admin - Galeri')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Manajemen Galeri</h1>
        <a href="{{ route('admin.posts.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Unggah Gambar</a>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 rounded bg-emerald-100 text-emerald-800">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.posts.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-6 gap-3">
        <div class="md:col-span-3">
            <label for="q" class="block text-sm text-gray-700">Cari Caption</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="kata kunci">
        </div>
        <div>
            <label for="tag" class="block text-sm text-gray-700">Tag</label>
            <input type="text" id="tag" name="tag" value="{{ request('tag') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="mis. kajian">
        </div>
        <div>
            <label for="active" class="block text-sm text-gray-700">Status Aktif</label>
            <select id="active" name="active" class="mt-1 w-full border rounded px-3 py-2">
                <option value="" @if(request('active')===null || request('active')==='') selected @endif>Semua</option>
                <option value="true" @if(request('active')==='true') selected @endif>Aktif</option>
                <option value="false" @if(request('active')==='false') selected @endif>Tidak Aktif</option>
            </select>
        </div>
        <div class="md:col-span-1 flex items-end space-x-2">
            <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Terapkan</button>
        </div>
    </form>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 border-b text-left">Thumb</th>
                    <th class="px-3 py-2 border-b text-left">Caption</th>
                    <th class="px-3 py-2 border-b text-left">Slug</th>
                    <th class="px-3 py-2 border-b text-left">Tags</th>
                    <th class="px-3 py-2 border-b text-left">Aktif</th>
                    <th class="px-3 py-2 border-b text-left">Publikasi</th>
                    <th class="px-3 py-2 border-b text-left">Dibuat</th>
                    <th class="px-3 py-2 border-b text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $it)
                    <tr>
                        <td class="px-3 py-2 border-b">
                            @php($thumb = $it->variantUrl('thumb') ?? $it->variantUrl('medium') ?? $it->variantUrl('large') ?? $it->variantUrl('max'))
                            @if ($thumb)
                                <img src="{{ $thumb }}?v={{ optional($it->updated_at)->timestamp }}"
                                     alt="{{ $it->alt_text }}"
                                     loading="lazy"
                                     decoding="async"
                                     class="h-16 w-24 object-contain rounded border bg-white"
                                     onerror="this.onerror=null;this.src='{{ asset('icon.jpg') }}';this.classList.add('bg-gray-100');">
                            @else
                                <div class="h-16 w-24 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-500">No Image</div>
                            @endif
                        </td>
                        <td class="px-3 py-2 border-b align-top">
                            <div class="text-sm text-gray-900">{{ $it->caption ?? '—' }}</div>
                            <div class="text-xs text-gray-500 break-all">{{ $it->alt_text ?? '—' }}</div>
                        </td>
                        <td class="px-3 py-2 border-b text-gray-700">{{ $it->slug }}</td>
                        <td class="px-3 py-2 border-b">
                            @if(is_array($it->tags) && count($it->tags))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($it->tags as $tg)
                                        <span class="inline-block px-2 py-0.5 text-xs rounded bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $tg }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 border-b">
                            @if($it->is_active)
                                <span class="inline-block px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-800">Aktif</span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs rounded bg-gray-200 text-gray-800">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 border-b text-sm text-gray-600">
                            {{ optional($it->published_at)->format('Y-m-d H:i') ?? '—' }}
                        </td>
                        <td class="px-3 py-2 border-b text-sm text-gray-600">
                            {{ optional($it->created_at)->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-3 py-2 border-b">
                            <a href="{{ route('admin.posts.edit', $it) }}" class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700">Edit</a>
                            <form method="POST" action="{{ route('admin.posts.destroy', $it) }}" class="inline" onsubmit="return confirm('Hapus item ini beserta semua varian gambar? Tindakan tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-sm rounded border border-red-300 text-red-700 hover:bg-red-50">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-gray-600">Belum ada item galeri.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between">
        <a href="{{ route('admin.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">Kembali ke Dashboard Admin</a>
        <div>
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection