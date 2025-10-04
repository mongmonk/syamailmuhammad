@extends('layouts.app')

@section('title', 'Beranda - ' . config('app.name', 'Syamail Muhammadiyah'))

@section('content')
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ config('app.name', 'Syamail Muhammadiyah') }}</h1>
            <p class="text-lg text-gray-600">Platform untuk menjelajahi bab-bab dan hadits Syamail secara responsif, dengan dukungan audio, bookmark, dan catatan pengguna.</p>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-12">
            <a href="{{ route('chapters.index') }}" class="inline-flex items-center px-6 py-3 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 transition-colors duration-200 shadow-sm">
                Jelajahi Bab
            </a>
            <a href="{{ route('search.form') }}" class="inline-flex items-center px-6 py-3 rounded-md bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors duration-200 shadow-sm">
                Cari Hadits
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 transition-transform duration-300 hover:scale-[1.02]">
                <div class="flex items-center mb-4">
                    <span class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">1</span>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Desain Responsif</h3>
                </div>
                <p class="text-gray-600">Antarmuka mobile-first yang nyaman digunakan di berbagai ukuran layar.</p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 transition-transform duration-300 hover:scale-[1.02]">
                <div class="flex items-center mb-4">
                    <span class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">2</span>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Audio & Navigasi</h3>
                </div>
                <p class="text-gray-600">Pemutar audio terintegrasi dan navigasi hadits yang mulus.</p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 transition-transform duration-300 hover:scale-[1.02]">
                <div class="flex items-center mb-4">
                    <span class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-700 font-bold">3</span>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Bookmark & Catatan</h3>
                </div>
                <p class="text-gray-600">Simpan bookmark dan buat catatan pribadi pada hadits yang penting.</p>
            </div>
        </div>

        <div class="max-w-3xl mx-auto">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Tipografi Arab</h3>
                <p class="arabic-text text-gray-700 mb-3" lang="ar" dir="rtl">النَّصُّ العَرَبِيُّ يُعرَضُ بِخَطٍّ واضِحٍ ومقروءٍ لِتَجْرِبَةِ قِراءَةٍ مُرْتَاحَةٍ.</p>
                <p class="translation-text text-gray-700">Teks Arab ditata agar jelas dan nyaman dibaca, mendukung pengalaman belajar yang baik.</p>
            </div>
        </div>
    </div>
</div>
@endsection
