@extends('layouts.app')

@section('title', 'Admin - Edit Hadits')

@section('content')
<div class="bg-white py-8">
  <div class="max-w-5xl mx-auto px-4">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-600 mb-4" aria-label="Breadcrumb">
      <ol class="list-reset inline-flex items-center space-x-2">
        <li><a class="text-gray-700 hover:text-emerald-700" href="{{ route('admin.index') }}">Admin</a></li>
        <li class="text-gray-400">/</li>
        <li><a class="text-gray-700 hover:text-emerald-700" href="{{ route('admin.hadiths.index') }}">Hadits</a></li>
        <li class="text-gray-400">/</li>
        <li class="text-gray-800">Edit</li>
      </ol>
    </nav>

    <div class="flex items-start justify-between mb-3">
      <div>
        <h1 class="text-2xl font-bold">Edit Hadits</h1>
        <p class="text-sm text-gray-600">Perbarui data hadits dengan tampilan rapi dan konsisten.</p>
      </div>
      <a href="{{ route('admin.hadiths.index') }}" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-gray-400 text-gray-700 rounded hover:bg-gray-400/20 transition-colors">Kembali</a>
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
            <div class="text-sm text-gray-600">
              <span class="mr-4">ID: <span class="font-medium text-gray-800">{{ $hadith->id }}</span></span>
              <span class="mr-4">Bab saat ini:
                <span class="font-medium text-gray-800">
                  @php
                    $currentChapter = $chapters->firstWhere('id', $hadith->chapter_id);
                  @endphp
                  @if($currentChapter)
                    Bab {{ $currentChapter->chapter_number }} — {{ $currentChapter->title }}
                  @else
                    #{{ $hadith->chapter_id }}
                  @endif
                </span>
              </span>
              <span>Nomor Hadits: <span class="font-medium text-gray-800">{{ $hadith->hadith_number }}</span></span>
            </div>
          </div>

          <form method="POST" action="{{ route('admin.hadiths.update', $hadith->id) }}" class="px-6 py-6 space-y-6" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- Bab -->
            <div>
              <label for="chapter_id" class="block text-sm font-medium text-gray-700 mb-1">Bab <span class="text-red-600">*</span></label>
              <select
                id="chapter_id"
                name="chapter_id"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                required
                aria-describedby="help_chapter"
              >
                @foreach ($chapters as $c)
                  <option value="{{ $c->id }}"
                    {{ (string)old('chapter_id', $hadith->chapter_id) === (string)$c->id ? 'selected' : '' }}>
                    Bab {{ $c->chapter_number }} — {{ $c->title }}
                  </option>
                @endforeach
              </select>
              <p id="help_chapter" class="mt-1 text-xs text-gray-500">Wajib pilih bab tujuan.</p>
              @error('chapter_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Nomor Hadits -->
            <div>
              <label for="hadith_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Hadits <span class="text-red-600">*</span></label>
              <input
                type="number"
                min="1"
                id="hadith_number"
                name="hadith_number"
                value="{{ old('hadith_number', $hadith->hadith_number) }}"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                placeholder="Masukkan nomor hadits (angka)"
                required
                aria-describedby="help_hadith_number"
              >
              <p id="help_hadith_number" class="mt-1 text-xs text-gray-500">Harus angka ≥ 1 dan unik dalam bab terpilih.</p>
              @error('hadith_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Teks Arab -->
            <div>
              <label for="arabic_text" class="block text-sm font-medium text-gray-700 mb-1">Teks Arab <span class="text-red-600">*</span></label>
              <textarea
                id="arabic_text"
                name="arabic_text"
                rows="5"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 text-right"
                style="font-family: 'Amiri Quran','Noto Naskh Arabic','KFGQPC Uthman Taha Naskh', serif; font-size: 1.15rem;"
                placeholder="ادخل نص الحديث هنا"
                required
                aria-describedby="help_arabic"
              >{{ old('arabic_text', $hadith->arabic_text) }}</textarea>
              <p id="help_arabic" class="mt-1 text-xs text-gray-500">Gunakan penulisan Arab yang benar. Area ini rata kanan dan menggunakan font Arab.</p>
              @error('arabic_text')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Terjemahan -->
            <div>
              <label for="translation" class="block text-sm font-medium text-gray-700 mb-1">Terjemahan <span class="text-red-600">*</span></label>
              <textarea
                id="translation"
                name="translation"
                rows="4"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                placeholder="Masukkan terjemahan hadits"
                required
                aria-describedby="help_trans"
              >{{ old('translation', $hadith->translation) }}</textarea>
              <p id="help_trans" class="mt-1 text-xs text-gray-500">Terjemahan bahasa Indonesia.</p>
              @error('translation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Catatan Kaki (opsional) -->
            <div>
              <label for="footnotes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Kaki (opsional)</label>
              <textarea
                id="footnotes"
                name="footnotes"
                rows="3"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                placeholder="Keterangan sumber, takhrij, atau catatan lain (opsional)"
                aria-describedby="help_footnotes"
              >{{ old('footnotes', $hadith->footnotes) }}</textarea>
              <p id="help_footnotes" class="mt-1 text-xs text-gray-500">Opsional. Bisa berisi catatan rujukan.</p>
              @error('footnotes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Audio Saat Ini (jika ada) -->
            @if ($hadith->audioFile)
            <div class="bg-gray-50 border rounded p-4">
              <div class="mb-2 text-sm font-medium text-gray-800">Audio saat ini</div>
              <div class="audio-player" data-audio-id="{{ $hadith->audioFile->id }}"></div>
              <div class="mt-2 text-xs text-gray-600">
                <span class="mr-3">ID Audio: <span class="font-medium text-gray-800">{{ $hadith->audioFile->id }}</span></span>
                @if(!is_null($hadith->audioFile->file_size))
                  <span>Ukuran: <span class="font-medium text-gray-800">{{ number_format($hadith->audioFile->file_size / 1024, 0) }} KB</span></span>
                @endif
              </div>
            </div>
            @endif

            <!-- Ganti / Unggah File Audio -->
            <div>
              <label for="audio_file" class="block text-sm font-medium text-gray-700 mb-1">File Audio (opsional)</label>
              <input
                type="file"
                id="audio_file"
                name="audio_file"
                accept="audio/*"
                class="block w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"
                aria-describedby="help_audio"
              >
              <p id="help_audio" class="mt-1 text-xs text-gray-500">
                Unggah file audio untuk hadits ini. Mengunggah file baru akan menggantikan audio lama. Format umum: MP3, OGG, WAV, M4A. Maksimal 15MB.
              </p>
              @error('audio_file')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Actions -->
            <div class="pt-2 flex items-center gap-3">
              <button type="submit" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-emerald-600 text-emerald-700 rounded hover:bg-emerald-600/50 hover:text-white transition-colors">Simpan</button>
              <a href="{{ route('admin.hadiths.index') }}" class="inline-flex items-center justify-center w-28 px-4 py-2 border border-gray-400 text-gray-700 rounded hover:bg-gray-400/20 transition-colors">Batal</a>
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
            <p>Pastikan memilih bab yang tepat sebelum mengubah nomor hadits.</p>
            <p>Nomor hadits harus unik dalam bab terpilih (tidak boleh duplikat).</p>
            <p>Teks Arab sebaiknya diperiksa kembali harakat dan ejaan.</p>
          </div>
        </div>
      </aside>
    </div>

    <div class="mt-6">
      <a href="{{ route('admin.index') }}" class="text-emerald-700 hover:underline">Kembali ke Dashboard Admin</a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
// Cache busting untuk audio saat halaman edit dibuka
(function bustAudioCache() {
    try {
        // Force reload audio elements to prevent caching
        const audioElements = document.querySelectorAll('audio');
        audioElements.forEach(function(audioEl) {
            const currentSrc = audioEl.src;
            if (currentSrc && currentSrc.includes('/audio/')) {
                // Add timestamp to force reload
                const separator = currentSrc.includes('?') ? '&' : '?';
                const newSrc = currentSrc + separator + '_reload=' + new Date().getTime();
                console.log('Busting audio cache on edit page:', {
                    oldSrc: currentSrc,
                    newSrc: newSrc
                });
                audioEl.src = newSrc;
                audioEl.load(); // Force reload
            }
        });
    } catch (e) {
        console.warn('Audio cache busting failed:', e);
    }
})();
});
</script>
@endpush