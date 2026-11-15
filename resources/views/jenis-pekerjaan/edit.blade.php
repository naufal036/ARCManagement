<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Jenis Pekerjaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-8 border border-gray-200">
                <form action="{{ route('jenis-pekerjaan.update', $jenisPekerjaan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nama_pekerjaan" value="{{ __('Nama Pekerjaan') }}" />
                            <x-text-input id="nama_pekerjaan" name="nama_pekerjaan" type="text" required autofocus value="{{ old('nama_pekerjaan', $jenisPekerjaan->nama_pekerjaan) }}" />
                            <x-input-error :messages="$errors->get('nama_pekerjaan')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="deskripsi" value="{{ __('Deskripsi') }}" />
                            <textarea id="deskripsi" name="deskripsi" rows="3" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('deskripsi', $jenisPekerjaan->deskripsi) }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-6">
                        <x-primary-button>{{ __('Perbarui') }}</x-primary-button>
                        <a href="{{ route('jenis-pekerjaan.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>