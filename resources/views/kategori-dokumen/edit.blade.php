<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('Edit Kategori Dokumen') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow sm:p-8">
                <form action="{{ route('kategori-dokumen.update', $kategoriDokumen) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="nama_kategori" value="{{ __('Nama Kategori') }}" />
                        <x-text-input id="nama_kategori" name="nama_kategori" type="text" class="mt-1 block w-full" :value="old('nama_kategori', $kategoriDokumen->nama_kategori)" required autofocus />
                        <x-input-error :messages="$errors->get('nama_kategori')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="deskripsi" value="{{ __('Deskripsi') }}" />
                        <textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi', $kategoriDokumen->deskripsi) }}</textarea>
                        <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Perbarui Kategori') }}</x-primary-button>
                        <a href="{{ route('kategori-dokumen.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Kembali') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
