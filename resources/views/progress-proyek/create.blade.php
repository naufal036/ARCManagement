
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Progress Proyek
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded-lg shadow-sm">

                <h3 class="text-lg font-bold mb-6">
                    Form Laporan Progress
                </h3>

                <form method="POST"
                      action="{{ route('progress-proyek.store') }}">

                    @csrf

                    <!-- Proyek -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">
                            Proyek
                        </label>

                        <select name="proyek_id"
                            class="w-full border-gray-300 rounded-lg" required>
                            <option value="">Pilih proyek</option>
                            @foreach ($proyeks as $proyek)
                                <option value="{{ $proyek->id }}" @selected(old('proyek_id') == $proyek->id)>
                                    {{ $proyek->kode_proyek }} - {{ $proyek->nama_proyek }}
                                </option>
                            @endforeach
                        </select>

                        @error('proyek_id')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Laporan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">
                            Tanggal Laporan
                        </label>

                        <input type="date"
                            name="tanggal_laporan"
                            value="{{ old('tanggal_laporan', date('Y-m-d')) }}"
                            required
                            class="w-full border-gray-300 rounded-lg">

                        @error('tanggal_laporan')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Persentase Progress -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">
                            Persentase Progress (%)
                        </label>

                        <input type="number"
                            name="persentase_progress"
                            min="0"
                            max="100"
                            step="0.01"
                            value="{{ old('persentase_progress') }}"
                            placeholder="Contoh: 75"
                            required
                            class="w-full border-gray-300 rounded-lg">

                        @error('persentase_progress')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Uraian Pekerjaan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">
                            Uraian Pekerjaan
                        </label>

                        <textarea name="uraian_pekerjaan"
                            rows="3"
                            required
                            placeholder="Jelaskan pekerjaan yang telah dilakukan..."
                            class="w-full border-gray-300 rounded-lg">{{ old('uraian_pekerjaan') }}</textarea>

                        @error('uraian_pekerjaan')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kendala -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">
                            Kendala
                        </label>

                        <textarea name="kendala"
                            rows="2"
                            class="w-full border-gray-300 rounded-lg">{{ old('kendala') }}</textarea>

                        @error('kendala')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Solusi -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">
                            Solusi
                        </label>

                        <textarea name="solusi"
                            rows="2"
                            class="w-full border-gray-300 rounded-lg">{{ old('solusi') }}</textarea>

                        @error('solusi')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">
                            Catatan
                        </label>

                        <textarea name="catatan"
                            rows="2"
                            class="w-full border-gray-300 rounded-lg">{{ old('catatan') }}</textarea>

                        @error('catatan')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rencana Selanjutnya -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">
                            Rencana Selanjutnya
                        </label>

                        <textarea name="rencana_selanjutnya"
                            rows="2"
                            class="w-full border-gray-300 rounded-lg">{{ old('rencana_selanjutnya') }}</textarea>

                        @error('rencana_selanjutnya')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="drive_url" class="block text-sm font-medium mb-2">Link Google Drive (opsional)</label>
                        <input id="drive_url" type="url" name="drive_url" value="{{ old('drive_url') }}" placeholder="https://drive.google.com/..." class="w-full border-gray-300 rounded-lg">
                        @error('drive_url') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tombol -->
                    <div class="flex justify-between">

                        <a href="{{ route('progress-proyek.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg">
                            Kembali
                        </a>

                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Simpan Progress
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
