@extends('layouts.app')

@section('title', 'Admin - Tambah Bab')

@section('content')
<div class="bg-white py-8">
  <div class="max-w-5xl mx-auto px-4">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-600 mb-4" aria-label="Breadcrumb">
      <ol class="list-reset inline-flex items-center space-x-2">
        <li><a class="text-gray-700 hover:text-emerald-700" href="{{ route('admin.index') }}">Admin</a></li>
        <li class="text-gray-400">/</li>
        <li><a class="text-gray-700 hover:text-emerald-700" href="{{ route('admin.chapters.index') }}">Bab</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-gray-800">Tambah</li>
      </ol>
    </nav>

    <div class="flex items-start justify-between mb-3">
      <div>
        <h1 class="text-2xl font-bold">Tambah Bab</h1>
        <p class="text-sm text-gray-600">Masukkan informasi bab secara rapi dan konsisten.</p>
      </div>
      <a href="{{ route('admin.chapters.index') }}" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-gray-400 text-gray-700 rounded hover:bg-gray-400/20 transition-colors">Kembali</a>
    </div>

    @if(session('status'))
      <div class="mb-4 p-3 bg-emerald-50 text-emerald-800 rounded">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
        <p class="font-semibold mb-1">Periksa kembali isian Anda:</p>
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Form Card -->
      <div class="md:col-span-2">
        <div class="bg-white border rounded shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b">
            <h2 class="text-sm font-semibold text-gray-800">Form Isian Bab</h2>
          </div>

          <form method="POST" action="{{ route('admin.chapters.store') }}" class="px-6 py-6 space-y-6">
            @csrf

            <!-- Nomor Bab -->
            <div>
              <label for="chapter_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Bab <span class="text-red-600">*</span></label>
              <input
                type="number"
                min="1"
                id="chapter_number"
                name="chapter_number"
                value="{{ old('chapter_number') }}"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                placeholder="Masukkan nomor bab (angka)"
                required
                aria-describedby="help_chapter_number"
              >
              <p id="help_chapter_number" class="mt-1 text-xs text-gray-500">Harus angka ≥ 1 dan unik.</p>
              @error('chapter_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Judul Bab -->
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Bab <span class="text-red-600">*</span></label>
              <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                placeholder="Contoh: Bab Pertama: Sifat Rupa Rasulullah SAW"
                maxlength="255"
                required
                aria-describedby="help_title"
              >
              <p id="help_title" class="mt-1 text-xs text-gray-500">Maksimal 255 karakter. Gunakan nama bab yang deskriptif.</p>
              @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Deskripsi -->
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
              <textarea
                id="description"
                name="description"
                rows="4"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                placeholder="Deskripsi singkat bab (opsional)"
                aria-describedby="help_description"
              >{{ old('description') }}</textarea>
              <p id="help_description" class="mt-1 text-xs text-gray-500">Opsional. Berikan ringkasan singkat isi bab.</p>
              @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div class="pt-2 flex items-center gap-3">
              <button type="submit" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-emerald-600 text-emerald-700 rounded hover:bg-emerald-600/50 hover:text-white transition-colors">Simpan</button>
              <a href="{{ route('admin.chapters.index') }}" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-gray-400 text-gray-700 rounded hover:bg-gray-400/20 transition-colors">Batal</a>
            </div>
          </form>
        </div>
      </div>

      <!-- Side Panel -->
      <aside class="md:col-span-1">
        <div class="bg-white border rounded shadow-sm">
          <div class="px-5 py-4 border-b">
            <h2 class="text-sm font-semibold text-gray-800">Panduan Singkat</h2>
          </div>
          <div class="px-5 py-4 space-y-3 text-sm text-gray-700">
            <p>Gunakan penamaan bab yang konsisten, contoh: <em>Bab Pertama: Sifat Rupa Rasulullah SAW</em>.</p>
            <p>Nomor bab mempengaruhi urutan tampil pada halaman publik.</p>
            <p>Pastikan deskripsi tidak terlalu panjang; ringkas dan informatif.</p>
          </div>
        </div>
      </aside>
    </div>
  </div>
</div>
@endsection