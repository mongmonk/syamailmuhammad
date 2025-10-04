@extends('layouts.app')
@section('title', 'Admin · Ubah Pengguna')
@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Ubah Pengguna</h1>
    <p class="text-gray-600">Perbarui status dan role untuk pengguna ini.</p>
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
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
      @csrf
      @method('PATCH')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
          <input type="text" value="{{ $user->id }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700" readonly>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
          <input type="text" value="{{ $user->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700" readonly>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="text" value="{{ $user->email }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700" readonly>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
          <input type="text" value="{{ $user->phone }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700" readonly>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-600 focus:border-emerald-600">
            <option value="">Tidak diubah</option>
            <option value="pending" @selected($user->status === 'pending')>pending</option>
            <option value="active" @selected($user->status === 'active')>active</option>
            <option value="banned" @selected($user->status === 'banned')>banned</option>
          </select>
        </div>
        <div>
          <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
          <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-600 focus:border-emerald-600">
            <option value="">Tidak diubah</option>
            <option value="user" @selected($user->role === 'user')>user</option>
            <option value="admin" @selected($user->role === 'admin')>admin</option>
          </select>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">Simpan</button>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">Batal</a>
      </div>
    </form>
  </div>

  <div class="mt-6">
    <a href="{{ route('admin.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">Kembali ke Dashboard Admin</a>
  </div>
</div>
@endsection