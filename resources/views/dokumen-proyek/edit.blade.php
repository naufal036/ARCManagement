<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('Edit Dokumen Proyek') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow sm:p-8">
                <form action="{{ route('dokumen-proyek.update', $dokumenProyek) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="nama_dokumen" value="{{ __('Nama Dokumen') }}" />
                        <x-text-input id="nama_dokumen" name="nama_dokumen" type="text" class="mt-1 block w-full" :value="old('nama_dokumen', $dokumenProyek->nama_dokumen)" required autofocus />
                        <x-input-error :messages="$errors->get('nama_dokumen')" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="proyek_id" value="{{ __('Proyek') }}" />
                            <select id="proyek_id" name="proyek_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach ($proyek as $item)
                                    <option value="{{ $item->id }}" @selected(old('proyek_id', $dokumenProyek->proyek_id) == $item->id)>{{ $item->nama_proyek }} ({{ $item->kode_proyek }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('proyek_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="kategori_dokumen_id" value="{{ __('Kategori Dokumen') }}" />
                            <select id="kategori_dokumen_id" name="kategori_dokumen_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach ($kategoriDokumen as $item)
                                    <option value="{{ $item->id }}" @selected(old('kategori_dokumen_id', $dokumenProyek->kategori_dokumen_id) == $item->id)>{{ $item->nama_kategori }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kategori_dokumen_id')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="deskripsi" value="{{ __('Deskripsi') }}" />
                        <textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi', $dokumenProyek->deskripsi) }}</textarea>
                        <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Perbarui Dokumen') }}</x-primary-button>
                        <a href="{{ route('dokumen-proyek.show', $dokumenProyek) }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Kembali ke Detail') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
