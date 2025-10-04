@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Masuk ke Akun Anda</h1>
                <p class="text-gray-600">Selamat datang kembali ke {{ config('app.name') }}</p>
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
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                               value="{{ old('email') }}">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                            <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                        </label>

                        <!-- Tautan lupa password akan diaktifkan setelah rute dibuat -->
                        <a href="{{ route('password.request') }}" class="text-sm text-emerald-600 hover:text-emerald-800">Lupa kata sandi?</a>
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-emerald-600 text-white rounded-md font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Masuk
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-6 text-sm text-gray-600">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-800 font-medium">Daftar</a>
            </div>
        </div>
    </div>
</div>
@endsection