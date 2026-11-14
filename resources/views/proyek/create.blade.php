<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrasi Proyek Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-8 border border-gray-200">
                <form action="{{ route('proyek.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="kode_proyek" value="{{ __('Kode Proyek') }}" />
                            <x-text-input id="kode_proyek" name="kode_proyek" type="text" required autofocus />
                            <x-input-error :messages="$errors->get('kode_proyek')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="nama_proyek" value="{{ __('Nama Proyek') }}" />
                            <x-text-input id="nama_proyek" name="nama_proyek" type="text" required />
                            <x-input-error :messages="$errors->get('nama_proyek')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="klien_id" value="{{ __('Klien') }}" />
                            <select id="klien_id" name="klien_id" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('Pilih Klien') }}</option>
                                @foreach ($klien as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_klien }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('klien_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="jenis_pekerjaan_id" value="{{ __('Jenis Pekerjaan') }}" />
                            <select id="jenis_pekerjaan_id" name="jenis_pekerjaan_id" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('Pilih Jenis Pekerjaan') }}</option>
                                @foreach ($jenisPekerjaan as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_pekerjaan }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('jenis_pekerjaan_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tanggal_mulai" value="{{ __('Tanggal Mulai') }}" />
                            <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" required />
                            <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tanggal_selesai" value="{{ __('Tanggal Selesai') }}" />
                            <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" />
                            <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="deskripsi" value="{{ __('Deskripsi') }}" />
                            <textarea id="deskripsi" name="deskripsi" rows="3" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('deskripsi') }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" value="{{ __('Status') }}" />
                            <select id="status" name="status" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Perencanaan">{{ __('Perencanaan') }}</option>
                                <option value="Proses">{{ __('Proses') }}</option>
                                <option value="Selesai">{{ __('Selesai') }}</option>
                                <option value="Ditunda">{{ __('Ditunda') }}</option>
                                <option value="Batal">{{ __('Batal') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-6">
                        <x-primary-button>{{ __('Simpan Proyek') }}</x-primary-button>
                        <a href="{{ route('proyek.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>