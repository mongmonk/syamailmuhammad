@extends('layouts.app')

@section('title', 'Pencarian Hadits')

@section('content')
<div class="py-10">
    <div class="max-w-5xl mx-auto px-4">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="px-6 py-5 border-b border-gray-200">
                <h1 dusk="page-title" class="text-2xl font-bold text-gray-900">Pencarian Hadits</h1>
                <p class="text-gray-600 mt-1">Cari pada teks Arab, terjemahan, dan tafsir. Gunakan filter untuk hasil yang lebih spesifik.</p>
            </div>

            <div class="p-6">
                <form method="GET" action="{{ route('search.advanced') }}" class="space-y-6" role="search" aria-label="Form Pencarian Hadits">
                    <div>
                        <label for="query" class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                        <input
                            type="text"
                            id="query"
                            name="query"
                            value="{{ old('query') }}"
                            placeholder="Contoh: iman, الصلاة, sedekah..."
                            minlength="2"
                            maxlength="100"
                            required
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        />
                        @error('query')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="chapter_id" class="block text-sm font-medium text-gray-700 mb-1">Bab</label>
                            <select
                                id="chapter_id"
                                name="chapter_id"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">Semua Bab</option>
                                @foreach ($chapters as $ch)
                                    <option value="{{ $ch->id }}" {{ (string)old('chapter_id') === (string)$ch->id ? 'selected' : '' }}>
                                        Bab {{ $ch->chapter_number }} - {{ $ch->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('chapter_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="limit" class="block text-sm font-medium text-gray-700 mb-1">Batas Hasil</label>
                                <input
                                    type="number"
                                    id="limit"
                                    name="limit"
                                    value="{{ old('limit', 20) }}"
                                    min="1"
                                    max="50"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                                @error('limit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mode" class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
                                <select
                                    id="mode"
                                    name="mode"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                >
                                    <option value="">Natural (Disarankan)</option>
                                    <option value="ts" {{ old('mode') === 'ts' ? 'selected' : '' }}>Query Lanjutan (tsquery)</option>
                                </select>
                                @error('mode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Pencarian mendukung teks Arab dan Latin.
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('search.form') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Reset</a>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">
                                Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-6 pb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Pencarian Populer</h2>
                        @if($popular->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach($popular as $item)
                                <a href="{{ route('search.advanced', ['query' => $item->query]) }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-sm border border-emerald-100">
                                    {{ $item->query }}
                                    <span class="ml-2 text-[11px] text-emerald-600/70">({{ $item->total }})</span>
                                </a>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-500">Belum ada data populer.</p>
                        @endif
                    </div>

                    @auth
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Riwayat Pencarian Anda</h2>
                        @if($history->isNotEmpty())
                        <ul class="divide-y divide-gray-200 border border-gray-200 rounded-md">
                            @foreach($history as $h)
                                <li class="px-4 py-3 flex items-center justify-between">
                                    <a href="{{ route('search.advanced', ['query' => $h->query]) }}" class="text-sm text-emerald-700 hover:text-emerald-800">
                                        {{ $h->query }}
                                    </a>
                                    <span class="text-xs text-gray-500">{{ $h->created_at?->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <form method="POST" action="{{ route('search.history.clear') }}" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-700">Hapus Riwayat</button>
                        </form>
                        @else
                        <p class="text-sm text-gray-500">Belum ada riwayat pencarian.</p>
                        @endif
                    </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- <div class="mt-6 text-sm text-gray-500">
            Endpoint JSON tersedia di <code class="px-1 py-0.5 bg-gray-100 border rounded">GET /search?query=...</code> untuk integrasi API.
        </div> -->
    </div>
</div>
@endsection