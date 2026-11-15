<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $dokumenProyek->nama_dokumen }}</h2>
            <a href="{{ route('dokumen-proyek.edit', $dokumenProyek) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit Dokumen') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status') === 'versi-dokumen-created')
                <div class="border-s-4 border-indigo-500 bg-indigo-50 p-4 text-sm text-indigo-700">{{ __('Versi dokumen berhasil diunggah.') }}</div>
            @endif

            <section class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Proyek') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dokumenProyek->proyek->nama_proyek }} ({{ $dokumenProyek->proyek->kode_proyek }})</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Kategori') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dokumenProyek->kategoriDokumen->nama_kategori }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Deskripsi') }}</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm text-gray-900">{{ $dokumenProyek->deskripsi ?: '-' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Unggah Versi Baru') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('PDF, Office, atau gambar; maksimal 20 MB per file.') }}</p>
                <form action="{{ route('dokumen-proyek.versi.store', $dokumenProyek) }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-5">
                    @csrf
                    <div>
                        <x-input-label for="file" value="{{ __('File Dokumen') }}" />
                        <input id="file" name="file" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" required class="mt-1 block w-full text-sm text-gray-700 file:me-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold">
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="catatan" value="{{ __('Catatan Versi') }}" />
                        <textarea id="catatan" name="catatan" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('catatan') }}</textarea>
                        <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
                    </div>
                    <x-primary-button>{{ __('Unggah Versi') }}</x-primary-button>
                </form>
            </section>

            <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Riwayat Versi') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Versi') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Nama File') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Diunggah Oleh') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Catatan') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($dokumenProyek->versiDokumen as $versi)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">v{{ $versi->nomor_versi }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $versi->nama_file }} <span class="text-xs">({{ number_format($versi->ukuran_file / 1024, 1) }} KB)</span></td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $versi->pengunggah?->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $versi->catatan ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <a href="{{ route('versi-dokumen.download', $versi) }}" class="font-medium text-indigo-600 hover:text-indigo-900">{{ __('Unduh') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('Belum ada versi file untuk dokumen ini.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <a href="{{ route('dokumen-proyek.index') }}" class="inline-block text-sm text-gray-600 hover:text-gray-900">{{ __('Kembali ke Daftar Dokumen') }}</a>
        </div>
    </div>
</x-app-layout>
