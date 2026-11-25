<div>
    <label for="project_assignment_id" class="mb-1 block font-medium text-gray-700">Penugasan</label>
    <select id="project_assignment_id" name="project_assignment_id" required class="w-full rounded border-gray-300 shadow-sm">
        <option value="">-- Pilih penugasan --</option>
        @foreach ($assignments as $assignment)
            <option value="{{ $assignment->id }}" @selected((string) old('project_assignment_id', request('project_assignment_id', $task?->project_assignment_id)) === (string) $assignment->id)>
                {{ $assignment->proyek?->nama_proyek }} — {{ $assignment->user?->name }} ({{ ucfirst($assignment->status) }})
            </option>
        @endforeach
    </select>
    @error('project_assignment_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="task_name" class="mb-1 block font-medium text-gray-700">Nama tugas</label>
    <input id="task_name" name="task_name" value="{{ old('task_name', $task?->task_name) }}" required class="w-full rounded border-gray-300 shadow-sm">
    @error('task_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="task_description" class="mb-1 block font-medium text-gray-700">Deskripsi</label>
    <textarea id="task_description" name="task_description" rows="3" class="w-full rounded border-gray-300 shadow-sm">{{ old('task_description', $task?->task_description) }}</textarea>
    @error('task_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="start_date" class="mb-1 block font-medium text-gray-700">Tanggal mulai</label>
    <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $task?->start_date?->format('Y-m-d')) }}" required class="w-full rounded border-gray-300 shadow-sm">
    @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="due_date" class="mb-1 block font-medium text-gray-700">Deadline</label>
    <input id="due_date" name="due_date" type="date" value="{{ old('due_date', $task?->due_date?->format('Y-m-d')) }}" required class="w-full rounded border-gray-300 shadow-sm">
    @error('due_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="priority" class="mb-1 block font-medium text-gray-700">Prioritas</label>
    <select id="priority" name="priority" required class="w-full rounded border-gray-300 shadow-sm">
        @foreach (['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi'] as $value => $label)
            <option value="{{ $value }}" @selected(old('priority', $task?->priority ?? 'medium') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('priority') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="status" class="mb-1 block font-medium text-gray-700">Status</label>
    <select id="status" name="status" required class="w-full rounded border-gray-300 shadow-sm">
        @foreach (['pending' => 'Menunggu', 'in_progress' => 'Berjalan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $task?->status ?? 'pending') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
