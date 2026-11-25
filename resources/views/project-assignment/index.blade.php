<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Penugasan Proyek</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                @if (session('success'))
                    <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">{{ session('error') }}</div>
                @endif

                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <form method="GET" action="{{ route('project-assignment.index') }}" class="flex flex-wrap gap-2">
                        <select name="project_id" class="rounded border-gray-300">
                            <option value="">Semua proyek</option>
                            @foreach ($proyeks as $proyek)
                                <option value="{{ $proyek->id }}" @selected((string) ($filters['project_id'] ?? '') === (string) $proyek->id)>{{ $proyek->nama_proyek }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="rounded border-gray-300">
                            <option value="">Semua status</option>
                            @foreach (['active' => 'Aktif', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="rounded bg-slate-700 px-4 py-2 text-white">Filter</button>
                        <a href="{{ route('project-assignment.index') }}" class="px-3 py-2 text-gray-600 hover:underline">Reset</a>
                    </form>
                    <a href="{{ route('project-assignment.create') }}" class="rounded bg-blue-600 px-4 py-2 font-semibold text-white">+ Tambah Penugasan</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Proyek</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Anggota</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Posisi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($assignments as $assignment)
                                <tr>
                                    <td class="px-4 py-3">{{ $assignment->proyek?->nama_proyek ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $assignment->user?->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $assignment->role_in_project }}</td>
                                    <td class="px-4 py-3">{{ $assignment->assigned_date->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($assignment->status) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('project-assignment.show', $assignment) }}" class="text-blue-700 hover:underline">Detail</a>
                                        <a href="{{ route('project-assignment.edit', $assignment) }}" class="ml-2 text-indigo-700 hover:underline">Edit</a>
                                        <form action="{{ route('project-assignment.destroy', $assignment) }}" method="POST" class="ml-2 inline"
                                            onsubmit="return confirm('Yakin ingin menghapus penugasan ini beserta tugasnya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-700 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada data penugasan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $assignments->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
