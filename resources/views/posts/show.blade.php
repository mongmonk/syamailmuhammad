@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <nav class="mb-6">
        <a href="{{ route('posts.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded">
            &larr; Kembali ke Artikel
        </a>
    </nav>

    <article class="bg-white border rounded p-6">
        <header>
            <h1 class="text-2xl font-bold">{{ $post->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                Dipublikasikan: {{ optional($post->created_at)->format('d M Y H:i') }}
            </p>
        </header>

        <section class="mt-6 leading-relaxed">
            {!! nl2br(e($post->body)) !!}
        </section>
    </article>
</div>
@endsection