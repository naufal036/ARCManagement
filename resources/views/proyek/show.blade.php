<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Proyek</h2>
    </x-slot>

    <div class="space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-[0_8px_24px_rgba(23,43,77,0.05)]">
                <div class="relative overflow-hidden bg-[#172B4D] px-6 py-7 text-white sm:px-8">@if ($proyek->cover_image_path)<img src="{{ asset('storage/'.$proyek->cover_image_path) }}" alt="Foto sampul {{ $proyek->nama_proyek }}" class="absolute inset-0 h-full w-full object-cover opacity-35"><div class="absolute inset-0 bg-[#172B4D]/75"></div>@endif<div class="relative z-10 max-w-3xl"><div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-blue-200"><a href="{{ route('proyek.index') }}" class="hover:text-white">Projects</a><span>/</span><span>{{ $proyek->kode_proyek }}</span></div><div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><h1 class="text-3xl font-bold tracking-tight">{{ $proyek->nama_proyek }}</h1><p class="mt-2 text-sm text-slate-300">{{ $proyek->klien?->nama_klien ?? 'Client belum diatur' }} · {{ $proyek->jenisPekerjaan?->nama_pekerjaan ?? 'Project' }}</p></div><span class="w-fit rounded-full bg-[#2563EB] px-3 py-1.5 text-xs font-semibold text-white">{{ $proyek->status }}</span></div></div><div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full border border-blue-300/20"></div><div class="pointer-events-none absolute right-10 top-12 h-40 w-40 rounded-full border border-blue-300/20"></div></div>
                <div class="grid gap-4 border-b border-[#E2E8F0] px-6 py-5 sm:grid-cols-4 sm:px-8"><div><p class="text-xs uppercase tracking-wide text-[#64748B]">Client / Instansi</p><p class="mt-1 text-sm font-semibold text-[#172B4D]">{{ $proyek->klien?->nama_klien ?? '—' }}</p><p class="mt-1 text-xs text-[#64748B]">{{ $proyek->klien?->jenis_client ?? 'Lainnya' }}{{ $proyek->klien?->npsn ? ' · NPSN '.$proyek->klien->npsn : '' }}</p></div><div><p class="text-xs uppercase tracking-wide text-[#64748B]">Location</p><p class="mt-1 text-sm font-semibold text-[#172B4D]">{{ $proyek->klien?->alamat ?? '—' }}</p><p class="mt-1 text-xs text-[#64748B]">{{ $proyek->klien?->email ?? $proyek->klien?->no_telepon ?? 'Kontak belum diatur' }}</p></div><div><p class="text-xs uppercase tracking-wide text-[#64748B]">Project year</p><p class="mt-1 text-sm font-semibold text-[#172B4D]">{{ $proyek->tanggal_mulai?->format('Y') ?? '—' }}</p></div><div><p class="text-xs uppercase tracking-wide text-[#64748B]">Progress reports</p><p class="mt-1 text-sm font-semibold text-[#2563EB]">{{ $proyek->progressProyek->count() }} laporan</p></div></div>
                <div class="flex flex-wrap items-center gap-2 px-4 pt-4 sm:px-6 sm:pt-5"><a href="#folders" class="rounded-xl bg-[#EFF6FF] px-4 py-2.5 text-sm font-semibold text-[#2563EB]">Folders</a><a href="{{ route('dokumen-proyek.index', ['proyek_id' => $proyek->id]) }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#64748B] transition hover:bg-[#F4F7FB]">Documents</a><a href="{{ route('progress-proyek.index', ['project_id' => $proyek->id]) }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#64748B] transition hover:bg-[#F4F7FB]">Progress</a><a href="{{ route('project-assignment.index', ['project_id' => $proyek->id]) }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#64748B] transition hover:bg-[#F4F7FB]">Team</a><a href="#project-info" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#64748B] transition hover:bg-[#F4F7FB]">Project info</a></div>
                <div id="folders" class="p-6 sm:p-8"><div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm font-semibold uppercase tracking-[0.14em] text-[#2563EB]">Project folders</p><h2 class="mt-1 text-xl font-bold text-[#172B4D]">Document categories</h2><p class="mt-1 text-sm text-[#64748B]">Struktur folder proyek tetap mengikuti arsip operasional ARC.</p></div><a href="{{ route('dokumen-proyek.create', ['proyek_id' => $proyek->id]) }}" class="inline-flex w-fit items-center gap-2 rounded-xl bg-[#172B4D] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#243B5A]">Upload document <span aria-hidden="true">+</span></a></div><div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">@foreach ($folderCategories as $folder)<a href="{{ route('dokumen-proyek.index', array_filter(['proyek_id' => $proyek->id, 'kategori_dokumen_id' => $folder['category_id']])) }}" class="group rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-4 transition hover:-translate-y-0.5 hover:border-[#93C5FD] hover:bg-[#EFF6FF]"><div class="flex items-start justify-between gap-3"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EFF6FF] text-[#2563EB] group-hover:bg-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h5l2 2h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" /></svg></span><span class="text-[#94A3B8] transition group-hover:text-[#2563EB]">→</span></div><p class="mt-4 font-bold text-[#172B4D]">{{ $folder['name'] }}</p><p class="mt-1 text-xs text-[#64748B]">{{ $folder['count'] }} file{{ $folder['count'] === 1 ? '' : 's' }}</p></a>@endforeach</div></div>
            </section>

            <div id="project-info" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div><span class="text-sm text-gray-500">Kode Proyek</span><p class="font-semibold">{{ $proyek->kode_proyek }}</p></div>
                <div><span class="text-sm text-gray-500">Nama Proyek</span><p>{{ $proyek->nama_proyek }}</p></div>
                <div><span class="text-sm text-gray-500">Klien</span><p>{{ $proyek->klien?->nama_klien ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Jenis Pekerjaan</span><p>{{ $proyek->jenisPekerjaan?->nama_pekerjaan ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Periode</span><p>{{ $proyek->tanggal_mulai }} s/d {{ $proyek->tanggal_selesai ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Status</span><p>{{ $proyek->status }}</p></div>
                <div><span class="text-sm text-gray-500">Deskripsi</span><p>{{ $proyek->deskripsi ?? '-' }}</p></div>

                <div>
                    <h3 class="mb-2 text-lg font-semibold">Progress Terbaru</h3>
                    @forelse ($proyek->progressProyek as $progress)
                        <a href="{{ route('progress-proyek.show', $progress) }}" class="block rounded border p-3 hover:bg-gray-50">
                            {{ $progress->tanggal_laporan?->format('d-m-Y') }} — {{ $progress->persentase_progress }}%
                        </a>
                    @empty
                        <p class="text-gray-500">Belum ada laporan progress.</p>
                    @endforelse
                </div>

                <a href="{{ route('proyek.index') }}" class="text-blue-600 hover:underline">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
