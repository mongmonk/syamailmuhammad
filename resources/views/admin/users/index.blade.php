@extends('layouts.app')
@section('title', 'Admin · Pengguna')
@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Pengguna</h1>
    <p class="text-gray-600">Kelola daftar pengguna. Filter berdasarkan nama, status, dan role.</p>
  </div>
  @if (session('status'))
  <div class="mb-6 p-4 rounded bg-emerald-100 text-emerald-800 text-sm">
    {{ session('status') }}
  </div>
  @endif
  <div class="bg-white rounded-lg shadow p-6 mb-6">
    <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Cari Nama</label>
        <input id="q" name="q" type="text" value="{{ request('q') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-600 focus:border-emerald-600" placeholder="Masukkan nama…">
      </div>
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-600 focus:border-emerald-600">
          <option value="">Semua</option>
          <option value="pending" @selected(request('status')==='pending')>pending</option>
          <option value="active" @selected(request('status')==='active')>active</option>
          <option value="banned" @selected(request('status')==='banned')>banned</option>
        </select>
      </div>
      <div>
        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
        <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-600 focus:border-emerald-600">
          <option value="">Semua</option>
          <option value="user" @selected(request('role')==='user')>user</option>
          <option value="admin" @selected(request('role')==='admin')>admin</option>
        </select>
      </div>
      <div class="flex items-end">
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">Terapkan</button>
      </div>
    </form>
  </div>
  <div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Daftar Pengguna</h2>
        <div class="flex items-center gap-3">
          <div class="text-sm text-gray-600 hidden sm:block">Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }} · Total {{ $users->total() }}</div>
          <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">
            Daftarkan Pengguna
          </a>
        </div>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse ($users as $user)
          <tr class="hover:bg-gray-50">
            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900">{{ $user->phone }}</td>
            <td class="px-6 py-3 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium
              @class([
                'bg-amber-100 text-amber-800' => $user->status === 'pending',
                'bg-emerald-100 text-emerald-800' => $user->status === 'active',
                'bg-red-100 text-red-800' => $user->status === 'banned',
              ])">{{ $user->status }}</span>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium
              @class([
                'bg-gray-100 text-gray-800' => $user->role === 'user',
                'bg-indigo-100 text-indigo-800' => $user->role === 'admin',
              ])">{{ $user->role }}</span>
            </td>
            <td class="px-6 py-3 whitespace-nowrap text-right text-sm">
              <div class="inline-flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700">Ubah</a>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
              Tidak ada data untuk filter saat ini.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t">
      {{ $users->links() }}
    </div>
  </div>
  <div class="mt-6">
    <a href="{{ route('admin.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">Kembali ke Dashboard Admin</a>
  </div>
</div>
@endsection