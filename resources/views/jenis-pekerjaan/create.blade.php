<x-app-layout>
    <x-slot name="header">
        <div><p class="text-sm font-medium text-[#0F766E]">Master Data</p><h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Tambah Jenis Pekerjaan</h2></div>
    </x-slot>
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <form action="{{ route('jenis-pekerjaan.store') }}" method="POST" class="mx-auto max-w-4xl rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-7">
            @csrf
            <p class="mb-6 text-sm text-[#5B6B80]">Kategori yang dibuat dapat langsung digunakan untuk registrasi proyek.</p>
            <div class="space-y-5">
                <div>
                    <x-input-label for="nama_pekerjaan" value="Nama Jenis Pekerjaan" />
                    <x-text-input id="nama_pekerjaan" name="nama_pekerjaan" type="text" required autofocus maxlength="100" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('nama_pekerjaan') }}" />
                    <x-input-error :messages="$errors->get('nama_pekerjaan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="deskripsi" value="Deskripsi" />
                    <textarea id="deskripsi" name="deskripsi" rows="5" class="mt-1 block w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">{{ old('deskripsi') }}</textarea>
                    <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                </div>
            </div>
            <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('jenis-pekerjaan.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#D7DFE8] px-5 text-sm font-semibold text-[#43546A] hover:bg-[#F5F8FA]">Batal</a>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0F766E] px-5 text-sm font-semibold text-white hover:bg-[#115E59]">Simpan Kategori</button>
            </div>
        </form>
    </div>
</x-app-layout>
