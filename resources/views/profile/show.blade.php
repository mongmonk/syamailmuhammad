@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Profil</h1>
                <p class="text-gray-600">Kelola informasi akun Anda.</p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-3 rounded bg-emerald-50 text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-3 rounded bg-red-50 text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Kartu Informasi Pengguna -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Akun</h2>
                <dl class="divide-y divide-gray-200">
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Nama</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->name }}</dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 break-all">{{ $user->email }}</dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->phone ?? '-' }}</dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Status Email</dt>
                        <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                            @if ($user->hasVerifiedEmail())
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-emerald-100 text-emerald-800">
                                    Terverifikasi
                                </span>
                            @else
                                <div class="space-y-2">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800">
                                        Belum Terverifikasi
                                    </span>
                                    <div>
                                        <a href="{{ route('verification.notice') }}" class="text-emerald-600 hover:text-emerald-800 text-sm">
                                            Verifikasi sekarang
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 flex items-center gap-3">
                    <a href="{{ route('profile.edit') }}"
                       class="px-4 py-2 bg-emerald-600 text-white rounded-md font-medium hover:bg-emerald-700">
                        Ubah Profil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md font-medium hover:bg-gray-200">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Aksi Verifikasi Email (jika belum) -->
            @unless ($user->hasVerifiedEmail())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Kirim Ulang Email Verifikasi</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Tidak menemukan email verifikasi? Kirim ulang tautan verifikasi ke alamat email Anda.
                    </p>
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-md font-medium hover:bg-emerald-700">
                            Kirim Ulang
                        </button>
                    </form>
                </div>
            @endunless

            <!-- Hapus Akun -->
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus Akun</h3>
                <p class="text-sm text-gray-600 mb-4">Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus.</p>
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.');">
                    @csrf
                    @method('DELETE')
                    <div class="max-w-sm">
                        <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                        <input id="delete_password" name="password" type="password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 mb-3">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md font-medium hover:bg-red-700">
                        Hapus Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection