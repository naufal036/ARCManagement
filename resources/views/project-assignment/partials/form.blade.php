<div>
    <label for="project_id" class="mb-1 block font-medium text-gray-700">Proyek</label>
    <select id="project_id" name="project_id" required class="w-full rounded border-gray-300 shadow-sm">
        <option value="">-- Pilih proyek --</option>
        @foreach ($proyeks as $proyek)
            <option value="{{ $proyek->id }}" @selected((string) old('project_id', $assignment?->project_id) === (string) $proyek->id)>{{ $proyek->nama_proyek }}</option>
        @endforeach
    </select>
    @error('project_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="user_id" class="mb-1 block font-medium text-gray-700">Anggota</label>
    <select id="user_id" name="user_id" required class="w-full rounded border-gray-300 shadow-sm">
        <option value="">-- Pilih anggota --</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected((string) old('user_id', $assignment?->user_id) === (string) $user->id)>{{ $user->name }}</option>
        @endforeach
    </select>
    @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="role_in_project" class="mb-1 block font-medium text-gray-700">Posisi di proyek</label>
    <input id="role_in_project" name="role_in_project" value="{{ old('role_in_project', $assignment?->role_in_project) }}" required class="w-full rounded border-gray-300 shadow-sm">
    @error('role_in_project') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="assigned_date" class="mb-1 block font-medium text-gray-700">Tanggal penugasan</label>
    <input id="assigned_date" name="assigned_date" type="date" value="{{ old('assigned_date', $assignment?->assigned_date?->format('Y-m-d')) }}" required class="w-full rounded border-gray-300 shadow-sm">
    @error('assigned_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="status" class="mb-1 block font-medium text-gray-700">Status penugasan</label>
    <select id="status" name="status" required class="w-full rounded border-gray-300 shadow-sm">
        @foreach (['active' => 'Aktif', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $assignment?->status ?? 'active') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
