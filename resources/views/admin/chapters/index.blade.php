@extends('layouts.app')

@section('title', 'Admin - Bab')

@section('content')
<div class="bg-white py-8">
  <div class="max-w-7xl mx-auto px-4">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-600 mb-4" aria-label="Breadcrumb">
      <ol class="list-reset inline-flex items-center space-x-2">
        <li><a class="text-gray-700 hover:text-emerald-700" href="{{ route('admin.index') }}">Admin</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-gray-800">Bab</li>
      </ol>
    </nav>

    <div class="flex items-start justify-between mb-4">
      <div>
        <h1 class="text-2xl font-bold">Daftar Bab</h1>
        <p class="text-sm text-gray-600">Kelola data bab dengan pencarian judul/deskripsi.</p>
      </div>
      <a href="{{ route('admin.chapters.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-emerald-600 text-emerald-700 rounded hover:bg-emerald-600/50 hover:text-white transition-colors">+ Tambah Bab</a>
    </div>

    @if(session('status'))
      <div class="mb-4 p-3 bg-emerald-50 text-emerald-800 rounded">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.chapters.index') }}" class="mb-4">
      <div class="flex gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Cari judul bab..." class="border px-3 py-2 rounded w-full">
        <button type="submit" class="px-3 py-2 bg-gray-200 rounded">Cari</button>
      </div>
    </form>

    <!-- Summary -->
    <div class="flex items-center justify-between mb-3">
      <div class="text-sm text-gray-600">
        Menampilkan <span class="font-semibold">{{ $chapters->count() }}</span> dari <span class="font-semibold">{{ $chapters->total() }}</span> data.
      </div>
      <div class="text-sm text-gray-600">
        Halaman <span class="font-semibold">{{ $chapters->currentPage() }}</span> dari <span class="font-semibold">{{ $chapters->lastPage() }}</span>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border rounded shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b">
        <h2 class="text-sm font-semibold text-gray-800">Data Bab</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">ID</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bab</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Judul</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Deskripsi</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Dibuat</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($chapters as $c)
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm text-gray-700">{{ $c->id }}</td>
                <td class="px-4 py-2 text-sm text-gray-700">Bab {{ $c->chapter_number }}</td>
                <td class="px-4 py-2 text-sm text-gray-800">
                  <a href="{{ route('chapters.show', $c->id) }}" class="text-emerald-700 hover:text-emerald-800 hover:underline" title="Buka Bab">
                    {{ $c->title }}
                  </a>
                </td>
                <td class="px-4 py-2 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($c->description, 80) }}</td>
                <td class="px-4 py-2 text-sm text-gray-500">{{ optional($c->created_at)->format('Y-m-d') }}</td>
                <td class="px-4 py-2 text-sm">
                  <div class="flex items-center">
                    <a href="{{ route('admin.chapters.edit', $c->id) }}" class="inline-flex items-center justify-center w-24 px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 mr-2">Edit</a>
                    <form action="{{ route('admin.chapters.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus bab ini? Tindakan tidak dapat dibatalkan.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="inline-flex items-center justify-center w-24 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada data untuk filter saat ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if ($chapters->hasPages())
        <div class="px-6 py-4 border-t">
          {{ $chapters->links() }}
        </div>
      @endif
    </div>

    <div class="mt-6">
      <a href="{{ route('admin.index') }}" class="text-emerald-700 hover:underline">Kembali ke Dashboard Admin</a>
    </div>
  </div>
</div>
@endsection