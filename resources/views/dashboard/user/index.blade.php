@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Dashboard Pengguna</h1>
    <p class="text-gray-600">Ringkasan akun dan akses fitur pribadi.</p>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Status Akun</h2>
      <p class="text-sm text-gray-600">
        Status: <span class="font-medium">{{ auth()->user()->status }}</span>
      </p>
      @if(auth()->user()->isPending())
      <p class="mt-2 text-amber-700 text-sm">Akun belum aktif. Akses konten bab/hadits dan formulir pencarian akan ditolak (403).</p>
      @elseif(auth()->user()->isBanned())
      <p class="mt-2 text-red-700 text-sm">Akun diblokir. Fitur pribadi tidak tersedia.</p>
      @else
      <p class="mt-2 text-emerald-700 text-sm">Akun aktif. Anda memiliki akses penuh sesuai kebijakan.</p>
      @endif
    </div>
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Navigasi Cepat</h2>
      <ul class="space-y-2">
        <li><a class="text-emerald-700 hover:underline" href="{{ route('profile.show') }}">Profil</a></li>
        <li><a class="text-emerald-700 hover:underline" href="{{ route('bookmarks.index') }}">Bookmark</a></li>
        <li><a class="text-emerald-700 hover:underline" href="{{ route('notes.index') }}">Catatan</a></li>
        <li><a class="text-emerald-700 hover:underline" href="{{ route('progress.index') }}">Progres Baca</a></li>
      </ul>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Kebijakan Akses</h2>
      <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
        <li>401: belum login.</li>
        <li>403: pending/banned sesuai endpoint.</li>
        <li>Konten bab/hadits/search form memerlukan status aktif.</li>
      </ul>
    </div>
  </div>

  <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Daftar Bab</h2>
      <p class="text-gray-500 text-sm">Jelajahi seluruh Bab dan mulai membaca hadits berdasarkan bab.</p>
      <a href="{{ route('chapters.index') }}" data-testid="btn-list-bab" class="mt-3 inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">List Bab</a>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Bookmark Terbaru</h2>
      <p class="text-gray-500 text-sm">Masuk ke halaman Bookmark untuk melihat daftar lengkap.</p>
      <a href="{{ route('bookmarks.index') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Lihat Bookmark</a>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Catatan Terbaru</h2>
      <p class="text-gray-500 text-sm">Masuk ke halaman Catatan untuk melihat daftar lengkap.</p>
      <a href="{{ route('notes.index') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Lihat Catatan</a>
    </div>
  </div>
</div>
@endsection