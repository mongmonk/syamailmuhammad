@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Buat Akun Baru</h1>
                <p class="text-gray-600">Daftar untuk menggunakan {{ config('app.name') }}</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input id="name" name="name" type="text" required autofocus autocomplete="name"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('name') }}">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" name="email" type="email" required autocomplete="email"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('email') }}">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon (opsional)</label>
                        <input id="phone" name="phone" type="text" autocomplete="tel"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('phone') }}">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter, mengandung huruf dan angka.</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-emerald-600 text-white rounded-md font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Daftar
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-6 text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-800 font-medium">Masuk</a>
            </div>
        </div>
    </div>
</div>
@endsection