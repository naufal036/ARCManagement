<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#2A6F97]">Arsip proyek</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Laporan Dokumentasi</h2>
            </div>
            <p class="text-sm text-[#5B6B80]">Telusuri foto dan PDF dari seluruh laporan progress.</p>
        </div>
    </x-slot>

    <div class="space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-6">
            <form method="GET" action="{{ route('laporan-dokumentasi.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4 xl:items-end">
                <div class="xl:col-span-1">
                    <label for="project_id" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-[#5B6B80]">Proyek</label>
                    <select id="project_id" name="project_id" class="w-full rounded-xl border-[#D7E0EA] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                        <option value="">Semua proyek</option>
                        @foreach ($proyeks as $proyek)
                            <option value="{{ $proyek->id }}" @selected((string) ($filters['project_id'] ?? '') === (string) $proyek->id)>{{ $proyek->nama_proyek }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-[#5B6B80]">Dari tanggal laporan</label>
                    <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-xl border-[#D7E0EA] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                </div>
                <div>
                    <label for="date_to" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-[#5B6B80]">Sampai tanggal laporan</label>
                    <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-xl border-[#D7E0EA] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                </div>
                <div>
                    <label for="search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-[#5B6B80]">Nama file</label>
                    <div class="flex gap-2">
                        <input id="search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama file..." class="min-w-0 w-full rounded-xl border-[#D7E0EA] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                        <button type="submit" class="rounded-xl bg-[#0A2342] px-4 py-2 text-sm font-semibold text-white hover:bg-[#123B63]">Filter</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            <div class="flex items-center justify-between border-b border-[#E3E8EF] px-5 py-5 sm:px-6">
                <div>
                    <h3 class="font-bold text-[#0A2342]">Dokumentasi progress</h3>
                    <p class="mt-1 text-sm text-[#5B6B80]">{{ $dokumentasi->total() }} file ditemukan</p>
                </div>
            </div>

            <div class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6 xl:grid-cols-3">
                @forelse ($dokumentasi as $file)
                    @php
                        $progress = $file->progressProyek;
                        $isImage = in_array(strtolower(pathinfo($file->path_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'], true);
                    @endphp
                    <article class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-[#F8FAFC]">
                        <div class="flex h-44 items-center justify-center overflow-hidden bg-[#E8EEF5]">
                            @if ($isImage && $progress)
                                <img src="{{ route('progress-proyek.dokumentasi.view', ['progress_proyek' => $progress, 'dokumentasi' => $file]) }}" alt="Preview {{ $file->nama_file_asli }}" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-[#2A6F97]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 3h9l3 3v15H6zM15 3v4h4M9 13h6M9 17h4" /></svg>
                                    <p class="mt-2 text-xs font-bold uppercase tracking-widest text-[#2A6F97]">PDF</p>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-3 p-4">
                            <div class="min-w-0">
                                <h4 class="truncate text-sm font-bold text-[#0F1B2D]" title="{{ $file->nama_file_asli }}">{{ $file->nama_file_asli }}</h4>
                                <p class="mt-1 truncate text-xs text-[#2A6F97]">{{ $progress?->proyek?->nama_proyek ?? 'Proyek tidak tersedia' }}</p>
                            </div>
                            <dl class="grid grid-cols-2 gap-2 text-xs text-[#5B6B80]">
                                <div><dt class="font-semibold text-[#8A97A8]">Tanggal laporan</dt><dd class="mt-0.5">{{ $progress?->tanggal_laporan?->format('d M Y') ?? '-' }}</dd></div>
                                <div><dt class="font-semibold text-[#8A97A8]">Pelapor</dt><dd class="mt-0.5 truncate">{{ $progress?->user?->name ?? '-' }}</dd></div>
                            </dl>
                            <p class="min-h-8 text-xs text-[#5B6B80]">{{ $file->keterangan ?: 'Tanpa keterangan' }}</p>
                            @if ($progress)
                                <div class="flex items-center gap-3 border-t border-[#E3E8EF] pt-3 text-xs font-semibold">
                                    <a href="{{ route('progress-proyek.dokumentasi.view', ['progress_proyek' => $progress, 'dokumentasi' => $file]) }}" target="_blank" rel="noopener" class="text-[#2A6F97] hover:text-[#0A2342]">Lihat</a>
                                    <a href="{{ route('progress-proyek.dokumentasi.download', ['progress_proyek' => $progress, 'dokumentasi' => $file]) }}" class="text-[#5B6B80] hover:text-[#0A2342]">Unduh</a>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="sm:col-span-2 xl:col-span-3">
                        <p class="px-6 py-12 text-center text-sm text-[#5B6B80]">Belum ada dokumentasi yang sesuai filter.</p>
                    </div>
                @endforelse
            </div>

            @if ($dokumentasi->hasPages())
                <div class="border-t border-[#E3E8EF] px-5 py-4 sm:px-6">
                    {{ $dokumentasi->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
