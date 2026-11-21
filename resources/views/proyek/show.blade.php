<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#0F766E]">Detail Proyek · {{ $proyek->kode_proyek }}</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">{{ $proyek->nama_proyek }}</h2>
            </div>
            <a href="{{ route('proyek.edit', $proyek) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0A2342] px-5 text-sm font-semibold text-white hover:bg-[#12385E]">Edit Proyek</a>
        </div>
    </x-slot>

    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Klien</p>
                <a href="{{ $proyek->klien ? route('klien.show', $proyek->klien) : route('klien.index') }}" class="mt-2 block font-semibold text-[#0A2342] hover:text-[#0F766E]">{{ $proyek->klien?->nama_klien ?? 'Tidak tersedia' }}</a>
            </div>
            <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Jenis Pekerjaan</p>
                <a href="{{ $proyek->jenisPekerjaan ? route('jenis-pekerjaan.show', $proyek->jenisPekerjaan) : route('jenis-pekerjaan.index') }}" class="mt-2 block font-semibold text-[#0A2342] hover:text-[#0F766E]">{{ $proyek->jenisPekerjaan?->nama_pekerjaan ?? 'Tidak tersedia' }}</a>
            </div>
            <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Status</p>
                <p class="mt-2 inline-flex rounded-full bg-[#E6F4F3] px-3 py-1 text-sm font-semibold text-[#0F766E]">{{ $proyek->status }}</p>
            </div>
            <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Anggaran</p>
                <p class="mt-2 font-semibold text-[#0A2342]">{{ $proyek->anggaran !== null ? 'Rp '.number_format((float) $proyek->anggaran, 0, ',', '.') : 'Belum diisi' }}</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-6">
                <h3 class="font-bold text-[#0A2342]">Informasi Proyek</h3>
                <dl class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Kode Proyek</dt><dd class="mt-1 font-semibold text-[#0A2342]">{{ $proyek->kode_proyek }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Nama Proyek</dt><dd class="mt-1 font-semibold text-[#0A2342]">{{ $proyek->nama_proyek }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Tanggal Mulai</dt><dd class="mt-1 text-sm text-[#43546A]">{{ $proyek->tanggal_mulai?->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Target Penyelesaian</dt><dd class="mt-1 text-sm text-[#43546A]">{{ $proyek->tanggal_selesai?->format('d M Y') ?? 'Belum ditentukan' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Deskripsi</dt><dd class="mt-1 whitespace-pre-line text-sm leading-6 text-[#43546A]">{{ $proyek->deskripsi ?: 'Deskripsi belum diisi.' }}</dd></div>
                </dl>
            </div>
            <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-6">
                <h3 class="font-bold text-[#0A2342]">Akses Modul</h3>
                <p class="mt-1 text-sm text-[#5B6B80]">Informasi berikut terhubung ke data proyek yang sama.</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    <a href="{{ route('contract.index') }}" class="rounded-xl border border-[#E3E8EF] p-4 transition hover:border-[#14B8A6] hover:bg-[#F8FAFC]"><span class="block text-xs text-[#718096]">Kontrak</span><span class="mt-1 block font-semibold text-[#0A2342]">{{ $proyek->contracts->count() }} kontrak terkait</span></a>
                    <a href="{{ route('project-assignment.index') }}" class="rounded-xl border border-[#E3E8EF] p-4 transition hover:border-[#14B8A6] hover:bg-[#F8FAFC]"><span class="block text-xs text-[#718096]">Penugasan</span><span class="mt-1 block font-semibold text-[#0A2342]">{{ $proyek->projectAssignments->count() }} penugasan terkait</span></a>
                    <a href="{{ route('dokumen-proyek.index') }}" class="rounded-xl border border-[#E3E8EF] p-4 transition hover:border-[#14B8A6] hover:bg-[#F8FAFC]"><span class="block text-xs text-[#718096]">Dokumen</span><span class="mt-1 block font-semibold text-[#0A2342]">{{ $proyek->dokumenProyek->count() }} dokumen terkait</span></a>
                    <a href="{{ route('progress-proyek.index') }}" class="rounded-xl border border-[#E3E8EF] p-4 transition hover:border-[#14B8A6] hover:bg-[#F8FAFC]"><span class="block text-xs text-[#718096]">Progress</span><span class="mt-1 block font-semibold text-[#0A2342]">{{ $proyek->progressProyek->count() }} laporan progress</span></a>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Kontrak Terkait</h3></div>
                <div class="divide-y divide-[#E3E8EF]">
                    @forelse ($proyek->contracts as $contract)
                        <a href="{{ route('contract.show', $contract) }}" class="block px-5 py-4 hover:bg-[#F8FAFC]"><p class="font-semibold text-[#0A2342]">{{ $contract->contract_name }}</p><p class="mt-1 text-xs text-[#718096]">{{ $contract->contract_number }} · Rp {{ number_format((float) $contract->contract_value, 0, ',', '.') }}</p></a>
                    @empty
                        <p class="px-5 py-7 text-sm text-[#718096]">Belum ada kontrak untuk proyek ini.</p>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Penugasan Tim</h3></div>
                <div class="divide-y divide-[#E3E8EF]">
                    @forelse ($proyek->projectAssignments as $assignment)
                        <a href="{{ route('project-assignment.show', $assignment) }}" class="block px-5 py-4 hover:bg-[#F8FAFC]"><p class="font-semibold text-[#0A2342]">{{ $assignment->user?->name ?? 'Pengguna tidak tersedia' }}</p><p class="mt-1 text-xs text-[#718096]">{{ $assignment->role_in_project ?: 'Peran belum diisi' }} · {{ \Carbon\Carbon::parse($assignment->assigned_date)->format('d M Y') }}</p></a>
                    @empty
                        <p class="px-5 py-7 text-sm text-[#718096]">Belum ada penugasan untuk proyek ini.</p>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Dokumen Proyek</h3></div>
                <div class="divide-y divide-[#E3E8EF]">
                    @forelse ($proyek->dokumenProyek as $dokumen)
                        <a href="{{ route('dokumen-proyek.show', $dokumen) }}" class="block px-5 py-4 hover:bg-[#F8FAFC]"><p class="font-semibold text-[#0A2342]">{{ $dokumen->nama_dokumen }}</p><p class="mt-1 text-xs text-[#718096]">{{ $dokumen->kategoriDokumen?->nama_kategori ?? 'Kategori dokumen' }}</p></a>
                    @empty
                        <p class="px-5 py-7 text-sm text-[#718096]">Belum ada dokumen untuk proyek ini.</p>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Laporan Progress</h3></div>
                <div class="divide-y divide-[#E3E8EF]">
                    @forelse ($proyek->progressProyek as $progress)
                        <a href="{{ route('progress-proyek.show', $progress) }}" class="block px-5 py-4 hover:bg-[#F8FAFC]"><div class="flex items-center justify-between gap-3"><p class="font-semibold text-[#0A2342]">{{ $progress->tanggal_laporan?->format('d M Y') }}</p><span class="text-sm font-bold text-[#0F766E]">{{ $progress->persentase_progress }}%</span></div><p class="mt-1 line-clamp-2 text-sm text-[#5B6B80]">{{ $progress->uraian_pekerjaan }}</p></a>
                    @empty
                        <p class="px-5 py-7 text-sm text-[#718096]">Belum ada laporan progress.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <a href="{{ route('proyek.index') }}" class="inline-flex min-h-10 items-center text-sm font-semibold text-[#2A6F97] hover:text-[#0A2342]">Kembali ke Proyek</a>
    </div>
</x-app-layout>
