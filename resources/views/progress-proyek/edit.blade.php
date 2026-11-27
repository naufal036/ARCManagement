<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Progress Proyek
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold mb-6">Edit Laporan Progress</h3>

                <form method="POST" action="{{ route('progress-proyek.update', $progress) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <p class="block text-sm font-medium mb-2">Proyek</p>
                        <div class="rounded-lg bg-gray-100 px-4 py-3 text-gray-700">
                            {{ $progress->proyek?->kode_proyek }} - {{ $progress->proyek?->nama_proyek ?? 'Belum tersedia' }}
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Proyek dan pembuat laporan tidak diubah pada proses edit.</p>
                    </div>

                    <div class="mb-4">
                        <label for="tanggal_laporan" class="block text-sm font-medium mb-2">Tanggal Laporan</label>
                        <input id="tanggal_laporan" type="date" name="tanggal_laporan" value="{{ old('tanggal_laporan', $progress->tanggal_laporan?->format('Y-m-d')) }}" required class="w-full border-gray-300 rounded-lg">
                        @error('tanggal_laporan') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="persentase_progress" class="block text-sm font-medium mb-2">Persentase Progress (%)</label>
                        <input id="persentase_progress" type="number" name="persentase_progress" min="0" max="100" step="0.01" value="{{ old('persentase_progress', $progress->persentase_progress) }}" required class="w-full border-gray-300 rounded-lg">
                        @error('persentase_progress') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="uraian_pekerjaan" class="block text-sm font-medium mb-2">Uraian Pekerjaan</label>
                        <textarea id="uraian_pekerjaan" name="uraian_pekerjaan" rows="3" required class="w-full border-gray-300 rounded-lg">{{ old('uraian_pekerjaan', $progress->uraian_pekerjaan) }}</textarea>
                        @error('uraian_pekerjaan') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    @foreach (['kendala' => 'Kendala', 'solusi' => 'Solusi', 'catatan' => 'Catatan', 'rencana_selanjutnya' => 'Rencana Selanjutnya'] as $field => $label)
                        <div class="mb-4">
                            <label for="{{ $field }}" class="block text-sm font-medium mb-2">{{ $label }}</label>
                            <textarea id="{{ $field }}" name="{{ $field }}" rows="2" class="w-full border-gray-300 rounded-lg">{{ old($field, $progress->{$field}) }}</textarea>
                            @error($field) <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    <div class="mb-6">
                        <label for="drive_url" class="block text-sm font-medium mb-2">Link Google Drive (opsional)</label>
                        <input id="drive_url" type="url" name="drive_url" value="{{ old('drive_url', $progress->drive_url) }}" placeholder="https://drive.google.com/..." class="w-full border-gray-300 rounded-lg">
                        @error('drive_url') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('progress-proyek.show', $progress) }}" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
