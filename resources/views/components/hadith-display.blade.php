@props(['hadith', 'showChapter' => true, 'showActions' => true, 'bookmark' => null, 'userNote' => null])

<div class="bg-white border border-gray-200 rounded-lg shadow-md p-6 mb-8">
    @if($showChapter && $hadith->chapter)
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
            {{ $hadith->chapter->title }}
        </h1>
        <p class="text-gray-600">{{ $hadith->chapter->description }}</p>
    </div>
    @endif
    
    <div class="border-t border-gray-200 pt-6">
        {{-- Kolom "Sumber" dihapus sesuai spesifikasi (hanya teks hadits, terjemahan, dan footnotes). --}}
        
        <!-- Arabic Text -->
        <div class="mb-8 text-right" dir="rtl">
            <div class="flex justify-between items-start mb-4">
                <h3 class="arabic-heading text-gray-800 mb-4"></h3>
                <h3 class="text-lg font-semibold text-emerald-900">
                    Hadits {{ $hadith->hadith_number }}
                </h3>
            </div>            
            <p class="arabic-text text-gray-700" lang="ar" dir="rtl">{!! nl2br($hadith->arabic_text) !!}</p>
        </div>
        
        <!-- Translation -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Terjemahan</h3>
            <p class="translation-text text-gray-700">{!! nl2br($hadith->translation) !!}</p>
        </div>
        
        {{-- Bagian Tafsir dihapus dari tampilan. Footnotes akan ditampilkan di bagian bawah dengan garis atas dan teks kecil. --}}
        
        <!-- Audio Player (if exists) -->
        @if($hadith->audioFile)
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Audio</h3>
            <div class="audio-player" data-audio-id="{{ $hadith->audioFile->id }}">
                <!-- Audio player akan di-generate oleh JavaScript -->
            </div>
        </div>
        @endif

        @if(!empty($hadith->footnotes))
            <hr class="my-6 border-gray-300" />
            <div class="footnotes text-xs text-gray-700">
                {!! nl2br($hadith->footnotes) !!}
            </div>
        @endif
         
         <!-- User Actions -->
         @if($showActions)
        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
            @auth
            <button id="bookmark-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors duration-200" data-hadith-id="{{ $hadith->id }}">
                {{ $bookmark ? 'Hapus Bookmark' : 'Bookmark' }}
            </button>
            <button id="note-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors duration-200" data-hadith-id="{{ $hadith->id }}">
                {{ $userNote ? 'Edit Catatan' : 'Tambah Catatan' }}
            </button>
            @endauth
        </div>
        @endif
    </div>
</div>