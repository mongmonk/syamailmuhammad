@extends('layouts.app')

@section('title', 'Bab ' . $chapter->chapter_number . ': ' . $chapter->title . ' - Buku ' . config('app.name', 'Syamail Muhammadiyah'))

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Chapter Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('chapters.index') }}" class="text-emerald-600 hover:text-emerald-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Kembali ke Daftar Bab
                    </a>
                    <div class="flex items-center">
                        <span class="bg-emerald-100 text-emerald-800 text-sm font-medium px-3 py-1 rounded-full">
                            Bab {{ $chapter->chapter_number }}
                        </span>
                    </div>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $chapter->title }}</h1>
                <p class="text-lg text-gray-600">{{ $chapter->description }}</p>
            </div>
            
            <!-- Font Size Controls -->
            <x-font-size-controls />
            
            <!-- Hadiths List -->
            @if($hadiths->count() > 0)
            <div class="space-y-6">
                @foreach($hadiths as $hadith)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Hadits {{ $hadith->hadith_number }}
                        </h3>
                    </div>
                    
                    <div class="mb-4">
                        <!-- Arabic Text -->
                        <div class="mb-4 text-right" dir="rtl">
                            <p class="arabic-text text-gray-700" lang="ar" dir="rtl">{{ $hadith->arabic_text }}</p>
                        </div>
                        
                        <!-- Translation -->
                        <p class="translation-text text-gray-700 mb-4">{{ $hadith->translation }}</p>
                        
                        @if(!empty($hadith->footnotes))
                            <hr class="my-6 border-gray-300" />
                            <div class="footnotes text-xs text-gray-700">
                                <ol class="list-decimal pl-5 space-y-1">
                                    @foreach($hadith->footnotes as $fn)
                                        <li value="{{ $fn['index'] }}">{{ $fn['content'] }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex justify-end">
                        <a href="{{ route('hadiths.show', $hadith->id) }}" class="text-emerald-600 hover:text-emerald-800 font-medium">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Belum ada hadits</h3>
                <p class="mt-1 text-gray-500">Bab ini belum memiliki hadits terkait.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- CSS untuk arabic-text sudah dipindahkan ke resources/css/app.css -->
@endsection