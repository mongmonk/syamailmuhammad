@extends('layouts.app')

@section('title', 'Daftar Bab - Buku ' . env('APP_NAME'))

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 dusk="page-title" class="text-3xl font-bold text-gray-900 mb-4">Kitab {{ env('APP_NAME') }}</h1>
                <p class="text-lg text-gray-600">
                    Karya Imam At-Tirmidzi yang menghimpun 56 bab yang menggambarkan pribadi dan fisik Rasulullah SAW secara terperinci
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($chapters as $chapter)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                    <a href="{{ route('chapters.show', $chapter->id) }}" class="block p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-emerald-100 flex items-center justify-center">
                                <span class="text-emerald-800 font-bold">{{ $chapter->chapter_number }}</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $chapter->title }}</h3>
                                <p class="text-gray-600 text-sm">{{ $chapter->description }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection