@extends('layouts.app')

@section('title', 'Hasil Pencarian')

@section('content')
<div class="py-10">
    <div class="max-w-5xl mx-auto px-4">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="px-6 py-5 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Hasil Pencarian</h1>
                <p class="text-gray-600 mt-1">
                    Kata kunci:
                    <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $query }}</span>
                </p>
                <div class="mt-2 text-sm text-gray-600">
                    @if(!empty($filters['chapter_id']))
                        <span class="font-medium">Filter diterapkan:</span>
                        <ul class="mt-1 list-disc list-inside">
                            <li>Bab: #{{ $filters['chapter_id'] }}</li>
                        </ul>
                    @else
                        <span class="text-gray-500">Tidak ada filter diterapkan.</span>
                    @endif
                </div>
            </div>

            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Total hasil:
                        <span class="px-2 py-1 rounded bg-gray-100 text-gray-800 border border-gray-200">{{ $count }}</span>
                        <span class="ml-2 text-gray-500">Limit: {{ $filters['limit'] ?? 20 }}</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('search.form') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Form Pencarian</a>
                        <a href="{{ route('search.advanced', ['query' => $query] + array_filter($filters ?? [])) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Ubah Filter</a>
                    </div>
                </div>
            </div>
        </div>

        @if($results->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md px-6 py-5">
                Tidak ada hasil ditemukan untuk kata kunci tersebut. Coba kata kunci lain atau kurangi filter.
            </div>
        @else
            <div class="space-y-6">
                @foreach($results as $h)
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200">
                            <div class="text-sm text-gray-600">
                                Bab {{ $h->chapter?->chapter_number }} • Hadits {{ $h->hadith_number }}
                            </div>
                            <a href="{{ route('hadiths.show', $h->id) }}" class="text-sm text-emerald-700 hover:text-emerald-800">
                                Lihat Detail (audio & catatan)
                            </a>
                        </div>
                        <div class="px-6 py-4">
                            <x-hadith-display :hadith="$h" :showChapter="true" :showActions="false" />
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-8 text-sm text-gray-500">
            Endpoint JSON tersedia di
            <code class="px-1 py-0.5 bg-gray-100 border rounded">GET /search?query={{ urlencode($query) }}</code>.
        </div>
    </div>
</div>
@endsection