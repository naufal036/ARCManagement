<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#2A6F97]">Pengaturan DMS</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Kategori Dokumen</h2>
            </div>
            <a href="{{ route('kategori-dokumen.create') }}" class="rounded-xl bg-[#0A2342] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#123B63]">+ Tambah Kategori</a>
        </div>
    </x-slot>

    <div class="space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success')) <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div> @endif
        @if (session('error')) <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div> @endif
        <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-[#F8FAFC] text-xs uppercase tracking-wide text-[#5B6B80]">
                        <tr><th class="px-6 py-3">Kategori</th><th class="px-6 py-3">Deskripsi</th><th class="px-6 py-3">Jumlah dokumen</th><th class="px-6 py-3 text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3E8EF]">
                        @forelse ($kategoriDokumen as $kategori)
                            <tr class="hover:bg-[#F8FAFC]">
                                <td class="px-6 py-4 font-semibold text-[#0F1B2D]">{{ $kategori->nama_kategori }}</td>
                                <td class="px-6 py-4 text-[#5B6B80]">{{ $kategori->deskripsi ?: '-' }}</td>
                                <td class="px-6 py-4 text-[#5B6B80]">{{ $kategori->dokumen_proyek_count }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a href="{{ route('kategori-dokumen.edit', $kategori) }}" class="font-semibold text-[#2A6F97]">Edit</a>
                                    <form method="POST" action="{{ route('kategori-dokumen.destroy', $kategori) }}" class="ml-3 inline" onsubmit="return confirm('Hapus kategori ini?');">
                                        @csrf @method('DELETE')
                                        <button class="font-semibold text-red-600">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-[#5B6B80]">Belum ada kategori dokumen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[#E3E8EF] px-6 py-4">{{ $kategoriDokumen->links() }}</div>
        </section>
    </div>
</x-app-layout>
