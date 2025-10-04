@extends('layouts.app')

@section('title', 'Reset Kata Sandi')

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Reset Kata Sandi</h1>
                <p class="text-gray-600">Masukkan kata sandi baru untuk akun Anda.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-emerald-50 text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

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
                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email"
                               name="email"
                               type="email"
                               autocomplete="email"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('email', $email) }}">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                        <input id="password"
                               name="password"
                               type="password"
                               autocomplete="new-password"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter, mengandung huruf dan angka.</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation"
                               name="password_confirmation"
                               type="password"
                               autocomplete="new-password"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-emerald-600 text-white rounded-md font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Reset Kata Sandi
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-6 text-sm text-gray-600">
                <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-800 font-medium">Kembali ke Halaman Masuk</a>
                <span class="mx-2">•</span>
                <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-800 font-medium">Daftar Akun Baru</a>
            </div>
        </div>
    </div>
</div>
@endsection