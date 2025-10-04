@extends('layouts.app')

@section('title', 'Bookmark Saya - ' . config('app.name', 'Syamail Muhammadiyah'))

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Bookmark Saya</h1>
                <a href="{{ route('chapters.index') }}" class="text-emerald-600 hover:text-emerald-800">Lihat Bab</a>
            </div>

            @if ($bookmarks->count() > 0)
                <div class="space-y-6">
                    @foreach ($bookmarks as $bookmark)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <div class="flex items-center space-x-3 mb-1">
                                        <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2 py-1 rounded-full">
                                            Bab {{ $bookmark->hadith->chapter->chapter_number }}
                                        </span>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">
                                            Hadits {{ $bookmark->hadith->hadith_number }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $bookmark->hadith->chapter->title }}
                                    </h3>
                                    <p class="text-sm text-gray-600">Sumber: {{ $bookmark->hadith->narration_source }}</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('hadiths.show', $bookmark->hadith->id) }}" class="px-3 py-2 text-sm bg-emerald-600 text-white rounded-md hover:bg-emerald-700">
                                        Lihat Hadits
                                    </a>
                                    <form method="POST" action="{{ route('bookmarks.destroy', $bookmark->hadith->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700" onclick="return confirm('Hapus bookmark ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if ($bookmark->notes)
                                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded p-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-1">Catatan Bookmark</h4>
                                    <p class="text-gray-700">{{ $bookmark->notes }}</p>
                                </div>
                            @endif

                            <div class="mt-4">
                                <p class="translation-text text-gray-700 line-clamp-3">{{ $bookmark->hadith->translation }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $bookmarks->links() }}
                </div>
            @else
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Belum ada bookmark</h3>
                    <p class="mt-1 text-gray-500">Tambahkan bookmark pada halaman detail hadits.</p>
                    <div class="mt-6">
                        <a href="{{ route('chapters.index') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Mulai Jelajahi</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection