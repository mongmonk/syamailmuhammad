@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Dashboard Admin</h1>
    <p class="text-gray-600">Kelola pengguna, posts, dan audit log dengan proteksi admin-only.</p>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Manajemen Pengguna</h2>
      <p class="text-sm text-gray-600">Lihat, filter, ubah status/role, buat atau hapus pengguna.</p>
      <div class="mt-3">
        <a href="{{ url('/admin/users') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Kelola Pengguna</a>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Manajemen Posts</h2>
      <p class="text-sm text-gray-600">Buat, edit, hapus post; pencarian judul dan toggle publish.</p>
      <div class="mt-3">
        <a href="{{ url('/admin/posts') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Kelola Posts</a>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Manajemen Bab</h2>
      <p class="text-sm text-gray-600">Kelola daftar Bab: buat, ubah, hapus, dan urutkan berdasarkan nomor bab.</p>
      <div class="mt-3">
        <a href="{{ url('/admin/chapters') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Kelola Bab</a>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Manajemen Hadits</h2>
      <p class="text-sm text-gray-600">Kelola hadis per bab: buat, ubah, hapus; nomor unik per bab.</p>
      <div class="mt-3">
        <a href="{{ url('/admin/hadiths') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Kelola Hadits</a>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Audit Log</h2>
      <p class="text-sm text-gray-600">Filter berdasarkan actor, aksi, resource, status; lihat detail dan ekspor.</p>
      <div class="mt-3 flex gap-2">
        <a href="{{ url('/admin/audit') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Lihat Audit Logs</a>
        <a href="{{ url('/admin/audit/export') }}" class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200">Export CSV</a>
      </div>
    </div>
  </div>
  <div class="mt-8">
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-lg font-semibold mb-2">Kebijakan & Catatan</h2>
      <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
        <li>Halaman ini hanya untuk admin aktif.</li>
        <li>Semua aksi akan terekam di Audit Log.</li>
        <li>Gunakan filter untuk pencarian cepat dan paginasi untuk navigasi.</li>
      </ul>
    </div>
  </div>
</div>
@endsection