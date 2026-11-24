<div>
    <label for="project_id" class="mb-1 block font-medium text-gray-700">Proyek</label>
    <select id="project_id" name="project_id" required class="w-full rounded border-gray-300 shadow-sm">
        <option value="">-- Pilih proyek --</option>
        @foreach ($proyeks as $proyek)
            <option value="{{ $proyek->id }}" @selected((string) old('project_id', $contract?->project_id) === (string) $proyek->id)>
                {{ $proyek->nama_proyek }}
            </option>
        @endforeach
    </select>
    @error('project_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
@foreach ([
    'contract_number' => ['Nomor kontrak', 'text'],
    'contract_name' => ['Nama kontrak', 'text'],
    'contract_date' => ['Tanggal kontrak', 'date'],
    'start_date' => ['Tanggal mulai', 'date'],
    'end_date' => ['Tanggal selesai', 'date'],
    'contract_value' => ['Nilai kontrak', 'text'],
] as $field => [$label, $type])
    <div>
        <label for="{{ $field }}" class="mb-1 block font-medium text-gray-700">{{ $label }}</label>
        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}"
                @if ($field === 'contract_value') inputmode="decimal" placeholder="Contoh: 2.500.000.000.000" @endif
            value="{{ old($field, $type === 'date' ? $contract?->{$field}?->format('Y-m-d') : $contract?->{$field}) }}" required class="w-full rounded border-gray-300 shadow-sm">
        @error($field) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
@endforeach
<div>
    <label for="status" class="mb-1 block font-medium text-gray-700">Status</label>
    <select id="status" name="status" required class="w-full rounded border-gray-300 shadow-sm">
        @foreach (['draft' => 'Draft', 'active' => 'Aktif', 'completed' => 'Selesai', 'terminated' => 'Dihentikan'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $contract?->status ?? 'active') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label for="file_kontrak" class="mb-1 block font-medium text-gray-700">File kontrak (PDF, maks. 5 MB)</label>
    <input id="file_kontrak" name="file_kontrak" type="file" accept="application/pdf" class="w-full rounded border-gray-300 shadow-sm">
    @if ($contract?->file_name)
        <p class="mt-1 text-sm text-gray-600">File saat ini: {{ $contract->file_name }}</p>
    @endif
    @error('file_kontrak') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
