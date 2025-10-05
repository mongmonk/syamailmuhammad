@extends('layouts.app')

@section('title', 'Tidak Berwenang')

@section('content')
<div class="container mx-auto px-4 py-16">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-8 text-center">
    <div class="mx-auto w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center" role="img" aria-label="Unauthorized">
      <svg class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M12 5a7 7 0 11-0.001 14.001A7 7 0 0112 5z" />
      </svg>
    </div>

    <h1 class="mt-6 text-2xl font-bold text-gray-900">401 - Tidak Berwenang</h1>
    <p class="mt-3 text-gray-700">{{ trans('errors.UNAUTHENTICATED') }}</p>
    <p class="mt-1 text-gray-500 text-sm">Silakan login terlebih dahulu untuk mengakses halaman atau fitur ini.</p>

    <div class="mt-6 mb-10 sm:mb-12 flex flex-col sm:flex-row gap-3 justify-center">
      <a href="{{ route('login') }}"
         class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Masuk
      </a>
      <a href="{{ url()->previous() }}"
         class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
        Kembali
      </a>
      <a href="{{ route('home') }}"
         class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
        Ke Beranda
      </a>
    </div>

    <!-- Separator dengan padding atas agar tidak mepet tombol -->
    <div class="mt-12 sm:mt-16 border-t border-gray-200"></div>
 
    <!-- Bagian detail di dalam kotak -->
    <div class="text-left p-5">
      <h2 class="text-sm font-semibold text-gray-600">Detail</h2>
      <ul class="mt-2 text-sm text-gray-600 list-disc list-inside space-y-1">
        <li>Kode: 401 UNAUTHENTICATED</li>
        <li>Rute: {{ optional(request()->route())->getName() ?? 'tidak diketahui' }}</li>
        <li>Jika Anda sudah login, muat ulang halaman atau periksa sesi Anda.</li>
      </ul>
    </div>
  </div>
</div>
@endsection