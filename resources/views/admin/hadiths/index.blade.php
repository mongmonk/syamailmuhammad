@extends('layouts.app')

@section('title', 'Admin - Hadits')

@section('content')
<div class="bg-white py-8">
  <div class="max-w-7xl mx-auto px-4">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-600 mb-4" aria-label="Breadcrumb">
      <ol class="list-reset inline-flex items-center space-x-2">
        <li><a class="text-gray-700 hover:text-emerald-700" href="{{ route('admin.index') }}">Admin</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-gray-800">Hadits</li>
      </ol>
    </nav>

    <div class="flex items-start justify-between mb-4">
      <div>
        <h1 class="text-2xl font-bold">Daftar Hadits</h1>
        <p class="text-sm text-gray-600">Kelola data hadits dengan pencarian dan filter bab.</p>
      </div>
      <a href="{{ route('admin.hadiths.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-emerald-600 text-emerald-700 rounded hover:bg-emerald-600/50 hover:text-white transition-colors">+ Tambah Hadits</a>
    </div>

    @if(session('status'))
      <div class="mb-4 p-3 bg-emerald-50 text-emerald-800 rounded">{{ session('status') }}</div>
    @endif

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.hadiths.index') }}" class="bg-white border rounded shadow-sm mb-6">
      <div class="px-6 py-4 border-b">
        <h2 class="text-sm font-semibold text-gray-800">Filter</h2>
      </div>
      <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label for="chapter_id" class="block text-sm font-medium text-gray-700 mb-1">Bab</label>
          <select id="chapter_id" name="chapter_id" class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
            <option value="">Semua bab</option>
            @foreach ($chapters as $c)
              <option value="{{ $c->id }}" {{ (string)request('chapter_id') === (string)$c->id ? 'selected' : '' }}>Bab {{ $c->chapter_number }} — {{ $c->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="md:col-span-2">
          <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
          <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Cari di teks Arab, terjemahan, atau catatan kaki" class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600" />
        </div>
      </div>
      <div class="px-6 py-4 border-t flex items-center gap-3">
        <button type="submit" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-emerald-600 text-emerald-700 rounded hover:bg-emerald-600/50 hover:text-white transition-colors">Terapkan</button>
        <a href="{{ route('admin.hadiths.index') }}" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-gray-400 text-gray-700 rounded hover:bg-gray-400/20 transition-colors">Reset</a>
      </div>
    </form>

    <!-- Summary -->
    <div class="flex items-center justify-between mb-3">
      <div class="text-sm text-gray-600">
        Menampilkan <span class="font-semibold">{{ $hadiths->count() }}</span> dari <span class="font-semibold">{{ $hadiths->total() }}</span> data.
      </div>
      <div class="text-sm text-gray-600">
        Halaman <span class="font-semibold">{{ $hadiths->currentPage() }}</span> dari <span class="font-semibold">{{ $hadiths->lastPage() }}</span>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border rounded shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b">
        <h2 class="text-sm font-semibold text-gray-800">Data Hadits</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">ID</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bab</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">No. Hadits</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Teks Arab</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Preview Terjemahan</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Dibuat</th>
              <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($hadiths as $h)
              @php
                $chapterLabel = $h->chapter ? ('Bab ' . $h->chapter->chapter_number . ' — ' . $h->chapter->title) : '-';
              @endphp
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm text-gray-700">{{ $h->id }}</td>
                <td class="px-4 py-2 text-sm text-gray-700">
                  <a href="{{ route('hadiths.show', $h->id) }}" class="text-emerald-700 hover:text-emerald-800 hover:underline" title="Buka Hadits">
                    {{ $chapterLabel }}
                  </a>
                </td>
                <td class="px-4 py-2 text-sm text-gray-700">{{ $h->hadith_number }}</td>
                <td class="px-4 py-2 text-sm text-right" style="font-family: 'Amiri Quran','Noto Naskh Arabic','KFGQPC Uthman Taha Naskh', serif;">
                  {{ \Illuminate\Support\Str::limit($h->arabic_text, 60) }}
                </td>
                <td class="px-4 py-2 text-sm text-gray-700">
                  {{ \Illuminate\Support\Str::limit($h->translation, 80) }}
                </td>
                <td class="px-4 py-2 text-sm text-gray-500">{{ optional($h->created_at)->format('Y-m-d') }}</td>
                <td class="px-4 py-2 text-sm">
                  <div class="flex items-center">
                    <a href="{{ route('admin.hadiths.edit', $h->id) }}" class="inline-flex items-center justify-center w-24 px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 mr-2">Edit</a>
                    <form method="POST" action="{{ route('admin.hadiths.destroy', $h->id) }}" class="inline" onsubmit="return confirm('Hapus hadits ini? Tindakan tidak dapat dibatalkan.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="inline-flex items-center justify-center w-24 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada data untuk filter saat ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if ($hadiths->hasPages())
        <div class="px-6 py-4 border-t">
          {{ $hadiths->links() }}
        </div>
      @endif
    </div>

    <div class="mt-6">
      <a href="{{ route('admin.index') }}" class="text-emerald-700 hover:underline">Kembali ke Dashboard Admin</a>
    </div>
  </div>
</div>
@endsection