@extends('layouts.app')

@section('title', 'Artikel')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Artikel</h1>

    <form method="GET" action="{{ route('posts.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-6 gap-3">
        <div class="md:col-span-5">
            <label for="q" class="block text-sm text-gray-700">Cari Judul</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="kata kunci">
        </div>
        <div class="md:col-span-1 flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Cari</button>
        </div>
    </form>

    @if ($posts->count() === 0)
        <div class="bg-white border rounded p-6 text-center text-gray-600">
            Belum ada artikel yang terbit.
        </div>
    @else
        <div class="bg-white border rounded divide-y">
            @foreach ($posts as $p)
                <article class="p-4">
                    <h2 class="text-lg font-semibold">
                        <a href="{{ route('posts.show', $p->slug) }}" class="text-emerald-700 hover:underline">
                            {{ $p->title }}
                        </a>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Dipublikasikan: {{ optional($p->created_at)->format('d M Y H:i') }}
                    </p>
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection