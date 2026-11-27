<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-[#2563EB]">Workspace overview</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#172B4D]">Selamat Datang di ARC Management</h2>
            </div>
            <p class="text-sm text-[#64748B]">Pantau proyek, dokumen, dan tim dalam satu tempat.</p>
        </div>
    </x-slot>

    <div class="space-y-8 px-4 py-8 sm:px-6 lg:px-8">
        <section class="relative overflow-hidden rounded-2xl bg-[#172B4D] px-6 py-7 text-white shadow-[0_14px_32px_rgba(23,43,77,0.18)] sm:px-8 sm:py-9">
            <div class="relative z-10 max-w-xl"><p class="text-sm font-semibold text-blue-200">Project &amp; Document Management</p><h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Ruang kerja terpusat untuk proyek yang lebih terkendali.</h1><p class="mt-4 max-w-lg text-sm leading-6 text-slate-300">Kelola milestone, dokumen, dan aktivitas tim konstruksi dengan visibilitas yang lebih jelas.</p></div>
            <div class="pointer-events-none absolute -right-8 -top-10 h-72 w-72 rounded-full border border-blue-300/20"></div><div class="pointer-events-none absolute right-10 top-10 h-48 w-48 rounded-full border border-blue-300/20"></div>
            <svg class="pointer-events-none absolute bottom-0 right-4 hidden h-52 w-[28rem] text-[#2563EB]/40 lg:block" viewBox="0 0 448 210" fill="none" aria-hidden="true"><path d="M30 210V88l78-42v164M108 210V34l78-30v206M186 210V70l78-34v174M264 210V18l78 30v162M342 210V90l76-40v160" stroke="currentColor" stroke-width="3"/><path d="M58 108h20M58 132h20M58 156h20M136 64h20M136 88h20M136 112h20M214 96h20M214 120h20M292 62h20M292 86h20M370 112h20M370 136h20" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg>
        </section>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ([
                ['label' => 'Total Proyek', 'value' => $totalProyek, 'route' => 'proyek.index', 'tone' => 'navy', 'icon' => 'M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'],
                ['label' => 'Total Klien', 'value' => $totalKlien, 'route' => 'klien.index', 'tone' => 'teal', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8'],
                ['label' => 'Total Kontrak', 'value' => $totalKontrak, 'route' => 'contract.index', 'tone' => 'blue', 'icon' => 'M6 3h9l3 3v15H6z M15 3v4h4 M9 12h6 M9 16h6'],
                ['label' => 'Total Dokumen', 'value' => $totalDokumen, 'route' => 'dokumen-proyek.index', 'tone' => 'teal', 'icon' => 'M6 3h8l4 4v14H6z M14 3v5h5 M9 13h6'],
                ['label' => 'Laporan Progress', 'value' => $totalProgress, 'route' => 'progress-proyek.index', 'tone' => 'amber', 'icon' => 'M3 3v18h18 M7 14l4-4 4 3 6-7'],
            ] as $stat)
                <a href="{{ route($stat['route']) }}" class="group rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] transition hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-[#5B6B80]">{{ $stat['label'] }}</p>
                            <p class="mt-3 text-3xl font-bold tracking-tight text-[#0A2342]">{{ number_format($stat['value']) }}</p>
                        </div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $stat['tone'] === 'navy' ? 'bg-[#E8EEF5] text-[#0A2342]' : ($stat['tone'] === 'teal' ? 'bg-[#E6F4F3] text-[#0F766E]' : ($stat['tone'] === 'blue' ? 'bg-[#E8F1FA] text-[#2A6F97]' : 'bg-[#FFF5D6] text-[#A16207]')) }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $stat['icon'] }}" /></svg>
                        </span>
                    </div>
                    <p class="mt-5 text-xs font-semibold text-[#2563EB] group-hover:text-[#172B4D]">Buka modul <span aria-hidden="true">→</span></p>
                </a>
            @endforeach
        </div>

        <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-6">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="font-bold text-[#0A2342]">Ringkasan Data Proyek</h3>
                    <p class="mt-1 text-xs text-[#5B6B80]">Jumlah proyek pada setiap status saat ini.</p>
                </div>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @forelse ($jumlahProyekPerStatus as $status)
                    <div class="rounded-xl border border-[#E3E8EF] bg-[#F8FAFC] px-4 py-3">
                        <p class="text-xs font-medium text-[#5B6B80]">{{ $status->status }}</p>
                        <p class="mt-1 text-2xl font-bold text-[#0A2342]">{{ number_format($status->total) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-[#5B6B80]">Belum ada data proyek.</p>
                @endforelse
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <section class="rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <div class="flex items-center justify-between border-b border-[#E3E8EF] px-5 py-4 sm:px-6">
                    <div>
                        <h3 class="font-bold text-[#0A2342]">Proyek Terbaru</h3>
                        <p class="mt-1 text-xs text-[#5B6B80]">Data proyek yang paling baru didaftarkan.</p>
                    </div>
                    <a href="{{ route('proyek.index') }}" class="text-sm font-semibold text-[#2A6F97] hover:text-[#0A2342]">Lihat semua</a>
                </div>
                <div class="divide-y divide-[#E3E8EF]">
                    @forelse ($proyekTerbaru as $proyek)
                        <a href="{{ route('proyek.show', $proyek) }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-[#F5F8FA] sm:px-6">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-[#0F1B2D]">{{ $proyek->nama_proyek }}</p>
                                <p class="mt-1 truncate text-xs text-[#64748B]">{{ $proyek->kode_proyek }} · {{ $proyek->klien?->nama_klien ?? 'Tanpa klien' }}</p>
                                <div class="mt-3 flex items-center gap-3"><div class="h-1.5 flex-1 overflow-hidden rounded-full bg-[#E2E8F0]"><div class="h-full rounded-full bg-[#2563EB]" style="width: {{ min((int) ($proyek->progress_proyek_max_persentase_progress ?? 0), 100) }}%"></div></div><span class="text-[11px] font-semibold text-[#64748B]">{{ (int) ($proyek->progress_proyek_max_persentase_progress ?? 0) }}%</span></div>
                            </div>
                            <span class="shrink-0 rounded-full bg-[#EFF6FF] px-3 py-1 text-xs font-semibold text-[#2563EB]">{{ $proyek->status }}</span>
                        </a>
                    @empty
                        <p class="px-6 py-10 text-center text-sm text-[#5B6B80]">Belum ada proyek.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <div class="border-b border-[#E3E8EF] px-5 py-4 sm:px-6">
                    <h3 class="font-bold text-[#172B4D]">Recent Activities</h3>
                    <p class="mt-1 text-xs text-[#64748B]">Aktivitas pelaporan progress terakhir.</p>
                </div>
                <div class="divide-y divide-[#E3E8EF]">
                    @forelse ($progressTerbaru as $progress)
                        <a href="{{ route('progress-proyek.show', $progress) }}" class="block px-5 py-4 transition hover:bg-[#F5F8FA] sm:px-6">
                            <div class="flex items-start justify-between gap-3">
                                <p class="truncate text-sm font-semibold text-[#0F1B2D]">{{ $progress->proyek?->nama_proyek ?? 'Proyek belum tersedia' }}</p>
                                <span class="shrink-0 text-xs font-bold text-[#2A6F97]">{{ $progress->persentase_progress }}%</span>
                            </div>
                            <p class="mt-1 line-clamp-2 text-xs text-[#5B6B80]">{{ $progress->uraian_pekerjaan }}</p>
                            <p class="mt-2 text-[11px] text-[#8A97A8]">{{ $progress->tanggal_laporan?->format('d M Y') }} · {{ $progress->user?->name ?? 'Pengguna' }}</p>
                        </a>
                    @empty
                        <p class="px-6 py-10 text-center text-sm text-[#5B6B80]">Belum ada laporan progress.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
