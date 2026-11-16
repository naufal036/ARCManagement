<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Tambah Kategori Dokumen</h2></x-slot>
    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('kategori-dokumen.store') }}" class="mx-auto max-w-2xl space-y-5 rounded-2xl border border-[#E3E8EF] bg-white p-6 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            @csrf
            <div><label for="nama_kategori" class="block text-sm font-semibold text-[#0F1B2D]">Nama kategori</label><input id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}" required class="mt-1 w-full rounded-xl border-[#D7E0EA] focus:border-[#14B8A6] focus:ring-[#14B8A6]">@error('nama_kategori')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label for="deskripsi" class="block text-sm font-semibold text-[#0F1B2D]">Deskripsi</label><textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 w-full rounded-xl border-[#D7E0EA] focus:border-[#14B8A6] focus:ring-[#14B8A6]">{{ old('deskripsi') }}</textarea></div>
            <div class="flex gap-3"><button class="rounded-xl bg-[#0A2342] px-4 py-2.5 text-sm font-semibold text-white">Simpan</button><a href="{{ route('kategori-dokumen.index') }}" class="rounded-xl border border-[#D7E0EA] px-4 py-2.5 text-sm font-semibold text-[#5B6B80]">Batal</a></div>
        </form>
    </div>
</x-app-layout>
