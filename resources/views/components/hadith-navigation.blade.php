@props(['previousHadith', 'nextHadith', 'chapter'])

<div class="flex justify-between items-center mt-8 mb-6">
    @if($previousHadith)
    <a href="{{ route('hadiths.show', $previousHadith->id) }}" class="flex items-center text-emerald-600 hover:text-emerald-800 transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        <div class="text-left">
            <div class="text-sm font-medium">Hadits Sebelumnya</div>
            @if($chapter)
            <div class="text-xs text-gray-500">Bab {{ $chapter->chapter_number }} - Hadits {{ $previousHadith->hadith_number }}</div>
            @endif
        </div>
    </a>
    @else
    <div></div> <!-- Empty div for spacing -->
    @endif
    
    @if($chapter)
    <div class="text-center">
        <a href="{{ route('chapters.show', $chapter->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali ke Bab {{ $chapter->chapter_number }}
        </a>
    </div>
    @endif
    
    @if($nextHadith)
    <a href="{{ route('hadiths.show', $nextHadith->id) }}" class="flex items-center text-emerald-600 hover:text-emerald-800 transition-colors duration-200">
        <div class="text-right">
            <div class="text-sm font-medium">Hadits Berikutnya</div>
            @if($chapter)
            <div class="text-xs text-gray-500">Bab {{ $chapter->chapter_number }} - Hadits {{ $nextHadith->hadith_number }}</div>
            @endif
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
    </a>
    @endif
</div>