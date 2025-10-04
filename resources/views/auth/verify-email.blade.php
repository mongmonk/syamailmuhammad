@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-lg mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Verifikasi Email Diperlukan</h1>
                <p class="text-gray-600 mt-2">
                    Terima kasih telah mendaftar. Sebelum melanjutkan, mohon verifikasi alamat email Anda dengan mengklik tautan yang kami kirim ke email Anda.
                </p>
                <p class="text-gray-600 mt-1">
                    Jika Anda tidak menerima email tersebut, Anda dapat meminta kami untuk mengirim ulang.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-3 rounded bg-emerald-50 text-emerald-700">
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

            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2 bg-emerald-600 text-white rounded-md font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <div class="text-center text-sm text-gray-600">
                    <p class="mb-2">Salah alamat email? Perbarui dari halaman profil setelah verifikasi.</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-6 text-sm text-gray-600">
                <p>Belum menerima email? Cek folder spam/junk pada email Anda.</p>
            </div>
        </div>
    </div>
</div>
@endsection