<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Klien') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-8 border border-gray-200">
                <form action="{{ route('klien.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nama_klien" value="{{ __('Nama Klien') }}" />
                            <x-text-input id="nama_klien" name="nama_klien" type="text" required autofocus />
                            <x-input-error :messages="$errors->get('nama_klien')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="jenis_client" value="{{ __('Jenis Client') }}" />
                            <select id="jenis_client" name="jenis_client" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">Pilih jenis client</option>@foreach (['Sekolah', 'Pemerintah', 'Swasta', 'Lainnya'] as $jenis)<option value="{{ $jenis }}" @selected(old('jenis_client') === $jenis)>{{ $jenis }}</option>@endforeach</select>
                            <x-input-error :messages="$errors->get('jenis_client')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="npsn" value="{{ __('NPSN (opsional)') }}" />
                            <x-text-input id="npsn" name="npsn" type="text" value="{{ old('npsn') }}" />
                            <x-input-error :messages="$errors->get('npsn')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="alamat" value="{{ __('Alamat') }}" />
                            <textarea id="alamat" name="alamat" rows="3" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat') }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="no_telepon" value="{{ __('No. Telepon') }}" />
                            <x-text-input id="no_telepon" name="no_telepon" type="text" />
                            <x-input-error :messages="$errors->get('no_telepon')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="{{ __('Email') }}" />
                            <x-text-input id="email" name="email" type="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-6">
                        <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                        <a href="{{ route('klien.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
