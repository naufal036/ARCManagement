<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Penugasan Proyek</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">{{ session('error') }}</div>
            @endif

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold">Informasi Penugasan</h3>
                    <a href="{{ route('project-assignment.edit', $projectAssignment) }}" class="text-indigo-700 hover:underline">Edit penugasan</a>
                </div>
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">Proyek</dt><dd class="font-semibold">{{ $projectAssignment->proyek?->nama_proyek ?? '-' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Anggota</dt><dd>{{ $projectAssignment->user?->name ?? '-' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Posisi</dt><dd>{{ $projectAssignment->role_in_project }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Tanggal penugasan</dt><dd>{{ $projectAssignment->assigned_date->format('d-m-Y') }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Status</dt><dd>{{ ucfirst($projectAssignment->status) }}</dd></div>
                </dl>
                <a href="{{ route('project-assignment.index') }}" class="mt-5 inline-block text-blue-700 hover:underline">Kembali ke daftar penugasan</a>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold">Tugas Proyek</h3>
                    <div class="flex gap-3">
                        <a href="{{ route('project-task.create', ['project_assignment_id' => $projectAssignment->id]) }}" class="text-blue-700 hover:underline">+ Tambah tugas</a>
                        <a href="{{ route('project-task.index', ['project_assignment_id' => $projectAssignment->id]) }}" class="text-indigo-700 hover:underline">Lihat daftar tugas</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama tugas</th>
                                <th class="px-4 py-3 text-left">Mulai</th>
                                <th class="px-4 py-3 text-left">Deadline</th>
                                <th class="px-4 py-3 text-left">Prioritas</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($projectAssignment->projectTasks as $task)
                                <tr>
                                    <td class="px-4 py-3"><a href="{{ route('project-task.show', $task) }}" class="text-blue-700 hover:underline">{{ $task->task_name }}</a></td>
                                    <td class="px-4 py-3">{{ $task->start_date?->format('d-m-Y') ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $task->due_date->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($task->priority) }}</td>
                                    <td class="px-4 py-3">{{ str_replace('_', ' ', ucfirst($task->status)) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-5 text-center text-gray-500">Belum ada tugas pada penugasan ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
