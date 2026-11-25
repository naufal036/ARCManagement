<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Kontrak</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">{{ session('error') }}</div>
            @endif

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold">Informasi Kontrak</h3>
                    <div class="flex gap-3">
                        <a href="{{ route('contract.edit', $contract) }}" class="text-indigo-700 hover:underline">Edit</a>
                        <form action="{{ route('contract.destroy', $contract) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus kontrak ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-700 hover:underline">Hapus kontrak</button>
                        </form>
                    </div>
                </div>
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">Nomor kontrak</dt><dd class="font-semibold">{{ $contract->contract_number }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Nama kontrak</dt><dd>{{ $contract->contract_name }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Proyek</dt><dd>{{ $contract->proyek?->nama_proyek ?? '-' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Tanggal kontrak</dt><dd>{{ $contract->contract_date->format('d-m-Y') }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Periode</dt><dd>{{ $contract->start_date->format('d-m-Y') }} – {{ $contract->end_date->format('d-m-Y') }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Nilai</dt><dd>Rp {{ number_format((float) $contract->contract_value, 0, ',', '.') }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Status</dt><dd>{{ ucfirst($contract->status) }}</dd></div>
                    <div>
                        <dt class="text-sm text-gray-500">File kontrak</dt>
                        <dd>
                            @if ($contract->file_path)
                                <a class="text-blue-700 hover:underline" href="{{ route('contract.file.download', $contract) }}">{{ $contract->file_name }}</a>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                </dl>
                <a href="{{ route('contract.index') }}" class="mt-5 inline-block text-blue-700 hover:underline">Kembali ke daftar kontrak</a>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold">Tambah Addendum</h3>
                <form action="{{ route('contract.addendums.store', $contract) }}" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
                    @csrf
                    <div>
                        <label for="addendum_number" class="mb-1 block font-medium">Nomor addendum</label>
                        <input id="addendum_number" name="addendum_number" value="{{ old('addendum_number') }}" required class="w-full rounded border-gray-300">
                        @error('addendum_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="addendum_date" class="mb-1 block font-medium">Tanggal addendum</label>
                        <input id="addendum_date" name="addendum_date" type="date" value="{{ old('addendum_date') }}" required class="w-full rounded border-gray-300">
                        @error('addendum_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block font-medium">Nilai sebelum</label>
                        <input value="{{ number_format((float) $contract->contract_value, 2, '.', '') }}" disabled class="w-full rounded border-gray-300 bg-gray-100">
                        <p class="mt-1 text-sm text-gray-500">Diambil dari nilai kontrak saat ini.</p>
                    </div>
                    <div>
                        <label for="value_after" class="mb-1 block font-medium">Nilai sesudah</label>
                        <input id="value_after" name="value_after" type="number" min="0" step="0.01" value="{{ old('value_after') }}" required class="w-full rounded border-gray-300">
                        @error('value_after') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="new_end_date" class="mb-1 block font-medium">Tanggal selesai baru (opsional)</label>
                        <input id="new_end_date" name="new_end_date" type="date" value="{{ old('new_end_date') }}" class="w-full rounded border-gray-300">
                        @error('new_end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="file_addendum" class="mb-1 block font-medium">File addendum (PDF, maks. 5 MB)</label>
                        <input id="file_addendum" name="file_addendum" type="file" accept="application/pdf" class="w-full rounded border-gray-300">
                        @error('file_addendum') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="mb-1 block font-medium">Keterangan</label>
                        <textarea id="description" name="description" rows="2" class="w-full rounded border-gray-300">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <button class="rounded bg-blue-600 px-4 py-2 font-semibold text-white">Simpan Addendum</button>
                    </div>
                </form>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold">Riwayat Addendum</h3>
                <div class="space-y-4">
                    @forelse ($contract->contractAddendums as $addendum)
                        <details class="rounded border p-4">
                            <summary class="cursor-pointer font-semibold">
                                {{ $addendum->addendum_number }} — {{ $addendum->addendum_date->format('d-m-Y') }}
                            </summary>
                            <div class="mt-3 space-y-3">
                                <p>Nilai sebelum: Rp {{ number_format((float) $addendum->value_before, 0, ',', '.') }}</p>
                                <p>Nilai sesudah: Rp {{ number_format((float) $addendum->value_after, 0, ',', '.') }}</p>
                                <p>Tanggal selesai baru: {{ $addendum->new_end_date?->format('d-m-Y') ?? '-' }}</p>
                                <p>Keterangan: {{ $addendum->description ?: '-' }}</p>
                                @if ($addendum->file_path)
                                    <p><a class="text-blue-700 hover:underline" href="{{ route('contract.addendums.file.download', [$contract, $addendum]) }}">{{ $addendum->file_name }}</a></p>
                                @endif
                                <form action="{{ route('contract.addendums.update', [$contract, $addendum]) }}" method="POST" enctype="multipart/form-data" class="grid gap-3 border-t pt-3 md:grid-cols-2">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="mb-1 block font-medium">Nomor addendum</label>
                                        <input name="addendum_number" value="{{ old('addendum_number', $addendum->addendum_number) }}" required class="w-full rounded border-gray-300">
                                        @error('addendum_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block font-medium">Tanggal addendum</label>
                                        <input name="addendum_date" type="date" value="{{ old('addendum_date', $addendum->addendum_date->format('Y-m-d')) }}" required class="w-full rounded border-gray-300">
                                        @error('addendum_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block font-medium">Nilai sebelum (otomatis)</label>
                                        <input value="{{ number_format((float) $contract->contract_value, 2, '.', '') }}" disabled class="w-full rounded border-gray-300 bg-gray-100">
                                    </div>
                                    <div>
                                        <label class="mb-1 block font-medium">Nilai sesudah</label>
                                        <input name="value_after" type="number" min="0" step="0.01" value="{{ old('value_after', $addendum->value_after) }}" required class="w-full rounded border-gray-300">
                                        @error('value_after') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block font-medium">Tanggal selesai baru</label>
                                        <input name="new_end_date" type="date" value="{{ old('new_end_date', $addendum->new_end_date?->format('Y-m-d')) }}" class="w-full rounded border-gray-300">
                                        @error('new_end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block font-medium">Ganti file addendum (PDF, maks. 5 MB)</label>
                                        <input name="file_addendum" type="file" accept="application/pdf" class="w-full rounded border-gray-300">
                                        @error('file_addendum') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block font-medium">Keterangan</label>
                                        <textarea name="description" rows="2" class="w-full rounded border-gray-300">{{ old('description', $addendum->description) }}</textarea>
                                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="flex gap-4 md:col-span-2">
                                        <button class="rounded bg-indigo-600 px-4 py-2 text-white">Perbarui Addendum</button>
                                        <span class="flex-1"></span>
                                        <button type="submit" form="delete-addendum-{{ $addendum->id }}" class="text-red-700 hover:underline"
                                            onclick="return confirm('Yakin ingin menghapus addendum ini?')">Hapus Addendum</button>
                                    </div>
                                </form>
                                <form id="delete-addendum-{{ $addendum->id }}" action="{{ route('contract.addendums.destroy', [$contract, $addendum]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </details>
                    @empty
                        <p class="text-gray-500">Belum ada addendum untuk kontrak ini.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
