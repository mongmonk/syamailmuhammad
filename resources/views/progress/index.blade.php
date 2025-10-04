@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-4">Progres Baca</h1>

    @if (session('status'))
        <div class="bg-green-100 text-green-800 border border-green-200 rounded p-3 mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if(empty($progress) || count($progress) === 0)
        <p class="text-gray-600">Belum ada progres baca.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 pr-4">Sumber</th>
                        <th class="text-left py-2 pr-4">ID</th>
                        <th class="text-left py-2 pr-4">Posisi</th>
                        <th class="text-left py-2">Diperbarui</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($progress as $item)
                        <tr class="border-b">
                            <td class="py-2 pr-4 capitalize">{{ $item['type'] }}</td>
                            <td class="py-2 pr-4">{{ $item['resource_id'] }}</td>
                            <td class="py-2 pr-4">{{ $item['position'] }}</td>
                            <td class="py-2">{{ $item['updated_at'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection