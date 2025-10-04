@extends('layouts.app')

@section('title', 'Admin - Edit Post')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Edit Post</h1>

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

    <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label for="title" class="block text-sm text-gray-700">Judul</label>
            <input id="title" name="title" type="text" value="{{ old('title', $post->title) }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label for="slug" class="block text-sm text-gray-700">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $post->slug) }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label for="body" class="block text-sm text-gray-700">Isi</label>
            <textarea id="body" name="body" rows="8" class="mt-1 w-full border rounded px-3 py-2">{{ old('body', $post->body) }}</textarea>
        </div>

        <div class="flex items-center space-x-2">
            <input type="hidden" name="is_published" value="0">
            <input id="is_published" name="is_published" type="checkbox" value="1" @if(old('is_published', $post->is_published ? '1' : '')==='1') checked @endif class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
            <label for="is_published" class="text-sm text-gray-700">Terbitkan</label>
        </div>

        <div class="flex items-center space-x-3">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Simpan Perubahan</button>
            <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Kembali</a>
        </div>
    </form>

    <div class="flex items-center justify-end mt-4">
        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Hapus post ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 border rounded text-red-700 border-red-300 hover:bg-red-50">Hapus</button>
        </form>
    </div>
</div>
@endsection