@extends('layouts.app')

@section('title', 'Catatan Saya - ' . config('app.name', 'Syamail Muhammadiyah'))

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Catatan Saya</h1>
                <a href="{{ route('chapters.index') }}" class="text-emerald-600 hover:text-emerald-800">Lihat Bab</a>
            </div>

            @if ($notes->count() > 0)
                <div class="space-y-6">
                    @foreach ($notes as $note)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <div class="flex items-center space-x-3 mb-1">
                                        <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2 py-1 rounded-full">
                                            Bab {{ $note->hadith->chapter->chapter_number }}
                                        </span>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">
                                            Hadits {{ $note->hadith->hadith_number }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $note->hadith->chapter->title }}
                                    </h3>
                                    <p class="text-sm text-gray-600">Sumber: {{ $note->hadith->narration_source }}</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('hadiths.show', $note->hadith->id) }}" class="px-3 py-2 text-sm bg-emerald-600 text-white rounded-md hover:bg-emerald-700">
                                        Lihat Hadits
                                    </a>
                                    <form method="POST" action="{{ route('notes.destroy', $note->hadith->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700" onclick="return confirm('Hapus catatan ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded p-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-1">Catatan</h4>
                                <p class="text-gray-700">{{ $note->note_content }}</p>
                            </div>

                            <div class="mt-4">
                                <p class="translation-text text-gray-700 line-clamp-3">{{ $note->hadith->translation }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $notes->links() }}
                </div>
            @else
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2v-7H3v7a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Belum ada catatan</h3>
                    <p class="mt-1 text-gray-500">Tambahkan catatan pada halaman detail hadits.</p>
                    <div class="mt-6">
                        <a href="{{ route('chapters.index') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Mulai Jelajahi</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection