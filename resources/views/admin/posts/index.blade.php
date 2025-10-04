@extends('layouts.app')

@section('title', 'Admin - Posts')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Daftar Post</h1>
        <a href="{{ route('admin.posts.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Tambah Post</a>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 rounded bg-emerald-100 text-emerald-800">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.posts.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label for="q" class="block text-sm text-gray-700">Cari Judul</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="kata kunci">
        </div>
        <div>
            <label for="published" class="block text-sm text-gray-700">Status Publikasi</label>
            <select id="published" name="published" class="mt-1 w-full border rounded px-3 py-2">
                <option value="" @if(request('published')===null || request('published')==='') selected @endif>Semua</option>
                <option value="true" @if(request('published')==='true') selected @endif>Terbit</option>
                <option value="false" @if(request('published')==='false') selected @endif>Draft</option>
            </select>
        </div>
        <div class="md:col-span-2 flex items-end space-x-2">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Terapkan</button>
            <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-3 py-2 border-b text-left">ID</th>
                    <th class="px-3 py-2 border-b text-left">Judul</th>
                    <th class="px-3 py-2 border-b text-left">Slug</th>
                    <th class="px-3 py-2 border-b text-left">Published</th>
                    <th class="px-3 py-2 border-b text-left">Created By</th>
                    <th class="px-3 py-2 border-b text-left">Dibuat</th>
                    <th class="px-3 py-2 border-b text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $p)
                    <tr>
                        <td class="px-3 py-2 border-b">{{ $p->id }}</td>
                        <td class="px-3 py-2 border-b">{{ $p->title }}</td>
                        <td class="px-3 py-2 border-b">{{ $p->slug }}</td>
                        <td class="px-3 py-2 border-b">
                            @if($p->is_published)
                                <span class="inline-block px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-800">Ya</span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs rounded bg-gray-200 text-gray-800">Tidak</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 border-b">{{ $p->created_by }}</td>
                        <td class="px-3 py-2 border-b">{{ optional($p->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-3 py-2 border-b">
                            <a href="{{ route('admin.posts.edit', $p) }}" class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700">Edit</a>
                            <form method="POST" action="{{ route('admin.posts.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus post ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-sm rounded border border-red-300 text-red-700 hover:bg-red-50">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-gray-600">Belum ada data post.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between">
        <a href="{{ route('admin.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">Kembali ke Dashboard Admin</a>
        <div>
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection