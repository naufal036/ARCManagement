<x-app-layout>
    <style>
        @media print {
            .app-sidebar,
            .app-topbar,
            nav[aria-label="Navigasi mobile"],
            .print-hidden {
                display: none !important;
            }

            .app-main {
                margin: 0 !important;
            }

            body,
            .app-main,
            main {
                background: #fff !important;
                color: #000 !important;
            }
        }
    </style>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#2A6F97]">Manajemen dokumen</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Laporan Arsip Dokumen</h2>
            </div>
            <div class="print-hidden flex flex-wrap gap-2">
                <a href="{{ route('laporan-arsip-dokumen.export.xlsx', request()->query()) }}" class="rounded-xl border border-[#D7E0EA] px-4 py-2.5 text-sm font-semibold text-[#0A2342]">Ekspor Excel</a>
                <a href="{{ route('laporan-arsip-dokumen.export.pdf', request()->query()) }}" class="rounded-xl bg-[#0A2342] px-4 py-2.5 text-sm font-semibold text-white">Ekspor PDF</a>
                <button type="button" onclick="window.print()" class="rounded-xl border border-[#D7E0EA] px-4 py-2.5 text-sm font-semibold text-[#5B6B80]">Cetak</button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <section class="print-hidden rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-6">
            <form method="GET" action="{{ route('laporan-arsip-dokumen.index') }}" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="search" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Nama / nomor dokumen</label>
                    <input id="search" name="search" value="{{ $filters['search'] ?? '' }}" class="mt-1 w-full rounded-xl border-[#D7E0EA] text-sm" placeholder="Cari arsip...">
                </div>
                <div>
                    <label for="project_id" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Proyek</label>
                    <select id="project_id" name="project_id" class="mt-1 w-full rounded-xl border-[#D7E0EA] text-sm">
                        <option value="">Semua proyek yang dapat diakses</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected((string) ($filters['project_id'] ?? '') === (string) $project->id)>{{ $project->nama_proyek }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="category_id" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Kategori</label>
                    <select id="category_id" name="category_id" class="mt-1 w-full rounded-xl border-[#D7E0EA] text-sm">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>{{ $category->kode_kategori }} · {{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Status verifikasi</label>
                    <select id="status" name="status" class="mt-1 w-full rounded-xl border-[#D7E0EA] text-sm">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="user_id" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Pengguna</label>
                    <select id="user_id" name="user_id" class="mt-1 w-full rounded-xl border-[#D7E0EA] text-sm">
                        <option value="">Semua pengguna</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="period" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Jenis periode</label>
                    <select id="period" name="period" class="mt-1 w-full rounded-xl border-[#D7E0EA] text-sm">
                        @foreach (['all' => 'Semua aktivitas', 'uploaded' => 'Dokumen diunggah', 'revised' => 'Dokumen direvisi', 'approved' => 'Dokumen disetujui', 'rejected' => 'Dokumen ditolak'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['period'] ?? 'all') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Dari tanggal</label>
                    <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="mt-1 w-full rounded-xl border-[#D7E0EA] text-sm">
                </div>
                <div>
                    <label for="date_to" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Sampai tanggal</label>
                    <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="mt-1 w-full rounded-xl border-[#D7E0EA] text-sm">
                </div>
                <div class="flex items-end gap-2 sm:col-span-2 xl:col-span-4">
                    <button class="rounded-xl bg-[#172B4D] px-4 py-2.5 text-sm font-semibold text-white">Terapkan Filter</button>
                    <a href="{{ route('laporan-arsip-dokumen.index') }}" class="rounded-xl border border-[#D7E0EA] px-4 py-2.5 text-sm font-semibold text-[#5B6B80]">Reset</a>
                </div>
            </form>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <article class="rounded-2xl border border-[#E3E8EF] bg-white p-5"><p class="text-sm text-[#5B6B80]">Total dokumen sesuai filter</p><p class="mt-2 text-3xl font-bold text-[#0A2342]">{{ number_format($summary['total']) }}</p></article>
            @foreach ($statuses as $status)
                <article class="rounded-2xl border border-[#E3E8EF] bg-white p-5"><p class="text-sm text-[#5B6B80]">{{ $status->label() }}</p><p class="mt-2 text-3xl font-bold text-[#0A2342]">{{ number_format($summary['statuses'][$status->value]) }}</p></article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <article class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white">
                <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Rekap kategori</h3></div>
                <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-[#F8FAFC] text-xs uppercase text-[#5B6B80]"><tr><th class="px-5 py-3">Kategori</th><th class="px-5 py-3 text-right">Dokumen</th></tr></thead><tbody class="divide-y divide-[#E3E8EF]">
                    @forelse ($summary['categories'] as $category)
                        <tr><td class="px-5 py-3">{{ $category->kode_kategori }} · {{ $category->nama_kategori }}</td><td class="px-5 py-3 text-right font-semibold">{{ $category->documents_count }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="px-5 py-6 text-center text-[#5B6B80]">Tidak ada data kategori.</td></tr>
                    @endforelse
                </tbody></table></div>
            </article>
            <article class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white">
                <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Rekap proyek</h3></div>
                <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-[#F8FAFC] text-xs uppercase text-[#5B6B80]"><tr><th class="px-5 py-3">Proyek</th><th class="px-5 py-3 text-right">Dokumen</th></tr></thead><tbody class="divide-y divide-[#E3E8EF]">
                    @forelse ($summary['projects'] as $project)
                        <tr><td class="px-5 py-3">{{ $project->kode_proyek }} · {{ $project->nama_proyek }}</td><td class="px-5 py-3 text-right font-semibold">{{ $project->documents_count }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="px-5 py-6 text-center text-[#5B6B80]">Tidak ada data proyek.</td></tr>
                    @endforelse
                </tbody></table></div>
            </article>
        </section>

        <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white">
            <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Daftar arsip dokumen</h3><p class="mt-1 text-sm text-[#5B6B80]">{{ $documents->total() }} dokumen ditemukan</p></div>
            <div class="overflow-x-auto"><table class="w-full min-w-[1050px] text-left text-sm"><thead class="bg-[#F8FAFC] text-xs uppercase text-[#5B6B80]"><tr><th class="px-5 py-3">Nomor / Nama</th><th class="px-5 py-3">Proyek</th><th class="px-5 py-3">Kategori</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Versi / Pengunggah</th><th class="px-5 py-3">Tanggal</th><th class="px-5 py-3"></th></tr></thead><tbody class="divide-y divide-[#E3E8EF]">
                @forelse ($documents as $document)
                    <tr><td class="px-5 py-4"><p class="font-mono text-xs text-[#2A6F97]">{{ $document->nomor_dokumen }}</p><p class="mt-1 font-semibold text-[#0F1B2D]">{{ $document->nama_dokumen }}</p></td><td class="px-5 py-4">{{ $document->proyek?->nama_proyek }}</td><td class="px-5 py-4">{{ $document->kategoriDokumen?->nama_kategori }}</td><td class="px-5 py-4">{{ $document->status_verifikasi->label() }}</td><td class="px-5 py-4">{{ $document->versiTerbaru ? 'v'.$document->versiTerbaru->nomor_versi : 'Belum ada versi' }}<p class="mt-1 text-xs text-[#5B6B80]">{{ $document->versiTerbaru?->pengunggah?->name ?? '-' }}</p></td><td class="px-5 py-4">{{ $document->updated_at?->format('d M Y H:i') }}</td><td class="px-5 py-4 text-right"><a href="{{ route('dokumen-proyek.show', $document) }}" class="font-semibold text-[#2A6F97]">Detail</a></td></tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-[#5B6B80]">Tidak ada dokumen sesuai filter.</td></tr>
                @endforelse
            </tbody></table></div>
            <div class="border-t border-[#E3E8EF] px-5 py-4">{{ $documents->links() }}</div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white">
            <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Aktivitas audit dokumen</h3><p class="mt-1 text-sm text-[#5B6B80]">Riwayat terstruktur untuk dokumen yang sesuai kewenangan dan filter.</p></div>
            <div class="overflow-x-auto"><table class="w-full min-w-[850px] text-left text-sm"><thead class="bg-[#F8FAFC] text-xs uppercase text-[#5B6B80]"><tr><th class="px-5 py-3">Waktu</th><th class="px-5 py-3">Pengguna</th><th class="px-5 py-3">Aktivitas</th><th class="px-5 py-3">Rincian</th></tr></thead><tbody class="divide-y divide-[#E3E8EF]">
                @forelse ($activities as $activity)
                    <tr><td class="whitespace-nowrap px-5 py-4">{{ $activity->created_at?->format('d M Y H:i:s') }}</td><td class="px-5 py-4">{{ $activity->user?->name ?? 'Pengguna dihapus' }}</td><td class="px-5 py-4">{{ str_replace('_', ' ', $activity->activity) }}</td><td class="px-5 py-4">{{ $activity->description ?: '-' }}</td></tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-[#5B6B80]">Belum ada aktivitas pada filter ini.</td></tr>
                @endforelse
            </tbody></table></div>
            <div class="border-t border-[#E3E8EF] px-5 py-4">{{ $activities->links() }}</div>
        </section>
    </div>
</x-app-layout>
