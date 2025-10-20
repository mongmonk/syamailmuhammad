@extends('layouts.app')
@section('title', 'Admin · Daftarkan Pengguna')
@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Daftarkan Pengguna</h1>
    <p class="text-gray-600">Form untuk membuat akun pengguna baru. Admin dapat menentukan status dan role.</p>
    @if (session('status'))
      <div class="mt-3 p-3 rounded bg-emerald-100 text-emerald-800 text-sm">
        {{ session('status') }}
      </div>
    @endif
    @if ($errors->any())
      <div class="mt-3 p-3 rounded bg-red-100 text-red-800 text-sm">
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>

  <div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
          <input id="name" name="name" type="text" required autocomplete="name"
                 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-600 focus:border-emerald-600"
                 value="{{ old('name') }}" placeholder="Nama pengguna">
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email (opsional)</label>
          <input id="email" name="email" type="email" autocomplete="email"
                 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-600 focus:border-emerald-600"
                 value="{{ old('email') }}" placeholder="nama@domain.com">
        </div>
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
          <input id="phone" name="phone" type="tel" required autocomplete="tel" inputmode="tel"
                 pattern="^(0\d{9,14}|\+?62[1-9]\d{7,13})$"
                 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-600 focus:border-emerald-600"
                 value="{{ old('phone') }}" placeholder="Contoh: 0822..., 62..., atau +62...">
          <p class="mt-1 text-xs text-gray-500">Format yang didukung: 0..., 62..., atau +62... (akan dinormalisasi).</p>
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
          <div class="relative" data-password-wrapper>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-emerald-600 focus:border-emerald-600 pr-10">
            <button type="button"
                    class="absolute inset-y-0 right-2 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none"
                    data-toggle="password" data-target="password" aria-label="Tampilkan kata sandi" aria-pressed="false">
              <svg data-eye xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
              <svg data-eye-off xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.34-4.153M6.18 6.18A9.957 9.957 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.132 5.225M3 3l18 18" />
              </svg>
            </button>
          </div>
          <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter, mengandung huruf dan angka.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select id="status" name="status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-600 focus:border-emerald-600">
            <option value="pending" @selected(old('status')==='pending')>pending</option>
            <option value="active" @selected(old('status')==='active')>active</option>
            <option value="banned" @selected(old('status')==='banned')>banned</option>
          </select>
        </div>
        <div>
          <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
          <select id="role" name="role"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-600 focus:border-emerald-600">
            <option value="user" @selected(old('role')==='user')>user</option>
            <option value="admin" @selected(old('role')==='admin')>admin</option>
          </select>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">
          Simpan & Daftarkan
        </button>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">Batal</a>
      </div>
    </form>
  </div>

  <div class="mt-6">
    <a href="{{ route('admin.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">Kembali ke Dashboard Admin</a>
  </div>
</div>
@endsection