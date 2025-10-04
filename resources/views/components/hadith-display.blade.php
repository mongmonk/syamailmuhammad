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
        <!-- Source -->
        <div class="mb-4">
            <span class="text-sm font-medium text-gray-900">Sumber:</span>
            <span class="text-sm text-gray-600 ml-2">{{ $hadith->narration_source }}</span>
        </div>
        
        <!-- Arabic Text -->
        <div class="mb-8 text-right" dir="rtl">
            <h3 class="arabic-heading text-gray-800 mb-4">نص الحديث</h3>
            <p class="arabic-text text-gray-700" lang="ar" dir="rtl">{{ $hadith->arabic_text }}</p>
        </div>
        
        <!-- Translation -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Terjemahan</h3>
            <p class="translation-text text-gray-700">{{ $hadith->translation }}</p>
        </div>
        
        <!-- Interpretation -->
        @if($hadith->interpretation)
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Tafsir</h3>
            <div class="interpretation-text">
                <p>{{ $hadith->interpretation }}</p>
            </div>
        </div>
        @endif
        
        <!-- Audio Player (if exists) -->
        @if($hadith->audioFile)
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Audio</h3>
            <div class="audio-player" data-audio-id="{{ $hadith->audioFile->id }}">
                <!-- Audio player akan di-generate oleh JavaScript -->
            </div>
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