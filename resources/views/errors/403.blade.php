@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="container mx-auto px-4 py-16">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-8 text-center">
    <div class="mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
      <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M12 5a7 7 0 11-0.001 14.001A7 7 0 0112 5z" />
      </svg>
    </div>

    <h1 class="mt-6 text-2xl font-bold text-gray-900">403 - Akses Ditolak</h1>

    @php
      $user = auth()->user();
      $isPending = $user && method_exists($user, 'isPending') ? $user->isPending() : false;
      $isBanned = $user && method_exists($user, 'isBanned') ? $user->isBanned() : false;
    @endphp

    @if($isBanned)
      <p class="mt-3 text-gray-700">{{ trans('errors.USER_STATUS_BANNED') }}</p>
      <p class="mt-1 text-gray-500 text-sm">Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.</p>
    @elseif($isPending)
      <p class="mt-3 text-gray-700">{{ trans('errors.USER_STATUS_NOT_ACTIVE') }}</p>
      <p class="mt-1 text-gray-500 text-sm">Akun Anda belum aktif. Anda masih dapat mengakses fitur pribadi seperti profil, bookmark, dan catatan.</p>
      <div class="mt-6">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
          Buka Dashboard
        </a>
      </div>
    @else
      <p class="mt-3 text-gray-700">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
    @endif

    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
      <a href="{{ url()->previous() }}"
         class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
        Kembali
      </a>
      <a href="{{ route('home') }}"
         class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
        Ke Beranda
      </a>
      @guest
      <a href="{{ route('login') }}"
         class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Masuk
      </a>
      @endguest
    </div>

    <hr class="my-8">

    <div class="text-left">
      <h2 class="text-sm font-semibold text-gray-600">Detail</h2>
      <ul class="mt-2 text-sm text-gray-500 list-disc pl-6 space-y-1">
        <li>Kode: 403 FORBIDDEN</li>
        @if($isBanned)
          <li>Alasan: {{ trans('errors.USER_STATUS_BANNED') }}</li>
        @elseif($isPending)
          <li>Alasan: {{ trans('errors.USER_STATUS_NOT_ACTIVE') }}</li>
        @else
          <li>Alasan: FORBIDDEN_ADMIN_ONLY atau kebijakan akses lainnya.</li>
        @endif
        <li>Rute: {{ optional(request()->route())->getName() ?? 'tidak diketahui' }}</li>
      </ul>
    </div>
  </div>
</div>
@endsection