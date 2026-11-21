<x-app-layout>
    <x-slot name="header">
        <div><p class="text-sm font-medium text-[#0F766E]">Master Data · {{ $klien->nama_klien }}</p><h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Edit Klien</h2></div>
    </x-slot>
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <form action="{{ route('klien.update', $klien) }}" method="POST" class="mx-auto max-w-4xl rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-7">
            @csrf
            @method('PUT')
            <p class="mb-6 text-sm text-[#5B6B80]">Nama klien harus unik. Proyek yang terhubung akan tetap memakai data klien ini.</p>
            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <x-input-label for="nama_klien" value="Nama Klien" />
                    <x-text-input id="nama_klien" name="nama_klien" type="text" required autofocus maxlength="150" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('nama_klien', $klien->nama_klien) }}" />
                    <x-input-error :messages="$errors->get('nama_klien')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" maxlength="100" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('email', $klien->email) }}" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="no_telepon" value="Nomor Telepon" />
                    <x-text-input id="no_telepon" name="no_telepon" type="tel" maxlength="20" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('no_telepon', $klien->no_telepon) }}" />
                    <x-input-error :messages="$errors->get('no_telepon')" class="mt-2" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="alamat" value="Alamat" />
                    <textarea id="alamat" name="alamat" rows="4" class="mt-1 block w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">{{ old('alamat', $klien->alamat) }}</textarea>
                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                </div>
            </div>
            <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('klien.show', $klien) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#D7DFE8] px-5 text-sm font-semibold text-[#43546A] hover:bg-[#F5F8FA]">Batal</a>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0F766E] px-5 text-sm font-semibold text-white hover:bg-[#115E59]">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
