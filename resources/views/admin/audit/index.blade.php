@extends('layouts.app')

@section('title', 'Admin - Audit Logs')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Audit Logs</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Dashboard</a>
            <a href="{{ route('admin.audit.export', request()->query()) }}" class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200">Export CSV</a>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 rounded bg-emerald-100 text-emerald-800">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.audit.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label for="actor" class="block text-sm text-gray-700">Actor ID</label>
            <input type="number" id="actor" name="actor" value="{{ old('actor', $filters['actor']) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="ID pengguna">
        </div>
        <div>
            <label for="action" class="block text-sm text-gray-700">Aksi</label>
            <input type="text" id="action" name="action" value="{{ old('action', $filters['action']) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="mis. login, update">
        </div>
        <div>
            <label for="resource_type" class="block text-sm text-gray-700">Resource Type</label>
            <input type="text" id="resource_type" name="resource_type" value="{{ old('resource_type', $filters['resource_type']) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="mis. post, user">
        </div>
        <div>
            <label for="resource_id" class="block text-sm text-gray-700">Resource ID</label>
            <input type="text" id="resource_id" name="resource_id" value="{{ old('resource_id', $filters['resource_id']) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="ID resource">
        </div>
        <div>
            <label for="status" class="block text-sm text-gray-700">Status</label>
            <select id="status" name="status" class="mt-1 w-full border rounded px-3 py-2">
                <option value="" @if(empty($filters['status'])) selected @endif>Semua</option>
                <option value="allow" @if($filters['status']==='allow') selected @endif>Allow</option>
                <option value="deny" @if($filters['status']==='deny') selected @endif>Deny</option>
            </select>
        </div>
        <div>
            <label for="reason" class="block text-sm text-gray-700">Reason Code</label>
            <input type="text" id="reason" name="reason" value="{{ old('reason', $filters['reason']) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="mis. POLICY_X">
        </div>
        <div>
            <label for="from" class="block text-sm text-gray-700">Dari (YYYY-MM-DD)</label>
            <input type="text" id="from" name="from" value="{{ old('from', $filters['from']) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="2025-10-01">
        </div>
        <div>
            <label for="to" class="block text-sm text-gray-700">Sampai (YYYY-MM-DD)</label>
            <input type="text" id="to" name="to" value="{{ old('to', $filters['to']) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="2025-10-31">
        </div>
        <div class="md:col-span-4 flex items-end space-x-2">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Terapkan</button>
            <a href="{{ route('admin.audit.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-3 py-2 border-b text-left">ID</th>
                    <th class="px-3 py-2 border-b text-left">Actor</th>
                    <th class="px-3 py-2 border-b text-left">Action</th>
                    <th class="px-3 py-2 border-b text-left">Resource</th>
                    <th class="px-3 py-2 border-b text-left">Status</th>
                    <th class="px-3 py-2 border-b text-left">Reason</th>
                    <th class="px-3 py-2 border-b text-left">IP</th>
                    <th class="px-3 py-2 border-b text-left">User Agent</th>
                    <th class="px-3 py-2 border-b text-left">Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="px-3 py-2 border-b">{{ $log->id }}</td>
                        <td class="px-3 py-2 border-b">
                            {{ optional($log->actor)->name ?? '—' }}
                            <div class="text-xs text-gray-500">#{{ $log->actor_id }}</div>
                        </td>
                        <td class="px-3 py-2 border-b">{{ $log->action }}</td>
                        <td class="px-3 py-2 border-b">
                            <div>{{ $log->resource_type ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $log->resource_id ?? '—' }}</div>
                        </td>
                        <td class="px-3 py-2 border-b">
                            @if($log->status === 'allow')
                                <span class="inline-block px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-800">Allow</span>
                            @elseif($log->status === 'deny')
                                <span class="inline-block px-2 py-1 text-xs rounded bg-red-100 text-red-800">Deny</span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs rounded bg-gray-200 text-gray-800">{{ $log->status }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 border-b">{{ $log->reason_code ?? '—' }}</td>
                        <td class="px-3 py-2 border-b">{{ $log->ip ?? '—' }}</td>
                        <td class="px-3 py-2 border-b">
                            <div class="truncate max-w-[240px]" title="{{ $log->user_agent }}">{{ $log->user_agent ?? '—' }}</div>
                        </td>
                        <td class="px-3 py-2 border-b">{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-6 text-center text-gray-600">Belum ada data audit.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between">
        <a href="{{ route('admin.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">Kembali ke Dashboard Admin</a>
        <div>
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection