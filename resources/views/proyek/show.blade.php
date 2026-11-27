<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#0F766E]">Detail Proyek · {{ $proyek->kode_proyek }}</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">{{ $proyek->nama_proyek }}</h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('proyek.edit', $proyek) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0A2342] px-5 text-sm font-semibold text-white hover:bg-[#12385E]">Edit Proyek</a>
                <a href="{{ route('proyek.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#D7DFE8] px-4 text-sm font-semibold text-[#43546A] hover:bg-[#F5F8FA]">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('checklist.index', $proyek) }}" class="rounded-xl bg-[#2563EB] px-4 py-2 text-sm font-semibold text-white">Checklist Saya</a>
            @if ($proyek->project_type === 'Pengawasan')
                <a href="{{ route('weekly-documentation.index', ['project_id' => $proyek->id]) }}" class="rounded-xl border border-[#D7E0EA] bg-white px-4 py-2 text-sm font-semibold text-[#0A2342]">Foto Dokumentasi</a>
                <a href="{{ route('laporan-pengawasan.index', ['project_id' => $proyek->id]) }}" class="rounded-xl border border-[#D7E0EA] bg-white px-4 py-2 text-sm font-semibold text-[#0A2342]">Laporan Pengawasan</a>
            @endif
        </div>
        <section class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-[0_8px_24px_rgba(23,43,77,0.05)]">
            <div class="relative overflow-hidden bg-[#172B4D] px-6 py-7 text-white sm:px-8">
                @if ($proyek->cover_image_path)
                    <img src="{{ asset('storage/'.$proyek->cover_image_path) }}" alt="Foto sampul {{ $proyek->nama_proyek }}" class="absolute inset-0 h-full w-full object-cover opacity-35">
                    <div class="absolute inset-0 bg-[#172B4D]/75"></div>
                @endif
                <div class="relative z-10 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-blue-200">
                        <a href="{{ route('proyek.index') }}" class="hover:text-white">Projects</a>
                        <span>/</span>
                        <span>{{ $proyek->kode_proyek }}</span>
                    </div>
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">{{ $proyek->nama_proyek }}</h1>
                            <p class="mt-2 text-sm text-slate-300">{{ $proyek->klien?->nama_klien ?? 'Client belum diatur' }} · {{ $proyek->jenisPekerjaan?->nama_pekerjaan ?? 'Project' }}</p>
                        </div>
                        <span class="w-fit rounded-full bg-[#2563EB] px-3 py-1.5 text-xs font-semibold text-white">{{ $proyek->status }}</span>
                    </div>
                </div>
                <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full border border-blue-300/20"></div>
                <div class="pointer-events-none absolute right-10 top-12 h-40 w-40 rounded-full border border-blue-300/20"></div>
            </div>
            <div class="grid gap-4 border-b border-[#E2E8F0] px-6 py-5 sm:grid-cols-4 sm:px-8">
                <div>
                    <p class="text-xs uppercase tracking-wide text-[#64748B]">Client / Instansi</p>
                    <p class="mt-1 text-sm font-semibold text-[#172B4D]">{{ $proyek->klien?->nama_klien ?? '—' }}</p>
                    <p class="mt-1 text-xs text-[#64748B]">{{ $proyek->klien?->jenis_client ?? 'Lainnya' }}{{ $proyek->klien?->npsn ? ' · NPSN '.$proyek->klien->npsn : '' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-[#64748B]">Anggaran</p>
                    <p class="mt-1 text-sm font-semibold text-[#172B4D]">{{ $proyek->anggaran !== null ? 'Rp '.number_format((float) $proyek->anggaran, 0, ',', '.') : 'Belum ditentukan' }}</p>
                    <p class="mt-1 text-xs text-[#64748B]">{{ $proyek->tanggal_mulai?->format('d M Y') }} s/d {{ $proyek->tanggal_selesai?->format('d M Y') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-[#64748B]">Location</p>
                    <p class="mt-1 text-sm font-semibold text-[#172B4D]">{{ $proyek->lokasi ?: ($proyek->klien?->alamat ?? '—') }}</p>
                    <p class="mt-1 text-xs text-[#64748B]">{{ $proyek->klien?->email ?? $proyek->klien?->no_telepon ?? 'Kontak belum diatur' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-[#64748B]">Progress reports</p>
                    <p class="mt-1 text-sm font-semibold text-[#2563EB]">{{ $proyek->progressProyek->count() }} laporan</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 px-4 pt-4 sm:px-6 sm:pt-5">
                <a href="#folders" class="rounded-xl bg-[#EFF6FF] px-4 py-2.5 text-sm font-semibold text-[#2563EB]">Folders</a>
                <a href="{{ route('dokumen-proyek.index', ['proyek_id' => $proyek->id]) }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#64748B] transition hover:bg-[#F4F7FB]">Documents</a>
                <a href="{{ route('progress-proyek.index', ['project_id' => $proyek->id]) }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#64748B] transition hover:bg-[#F4F7FB]">Progress</a>
                <a href="{{ route('project-assignment.index', ['project_id' => $proyek->id]) }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#64748B] transition hover:bg-[#F4F7FB]">Team</a>
                <a href="{{ route('contract.index', ['project_id' => $proyek->id]) }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#64748B] transition hover:bg-[#F4F7FB]">Contract</a>
            </div>
            <div id="folders" class="p-6 sm:p-8">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.14em] text-[#2563EB]">Project folders</p>
                        <h2 class="mt-1 text-xl font-bold text-[#172B4D]">Document categories</h2>
                        <p class="mt-1 text-sm text-[#64748B]">Struktur folder proyek tetap mengikuti arsip operasional ARC.</p>
                    </div>
                    <a href="{{ route('dokumen-proyek.create', ['proyek_id' => $proyek->id]) }}" class="inline-flex w-fit items-center gap-2 rounded-xl bg-[#172B4D] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#243B5A]">Upload document <span aria-hidden="true">+</span></a>
                </div>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($folderCategories as $folder)
                        <a href="{{ route('dokumen-proyek.index', array_filter(['proyek_id' => $proyek->id, 'kategori_dokumen_id' => $folder['category_id']])) }}" class="group rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] p-4 transition hover:-translate-y-0.5 hover:border-[#93C5FD] hover:bg-[#EFF6FF]">
                            <div class="flex items-start justify-between gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EFF6FF] text-[#2563EB] group-hover:bg-white">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h5l2 2h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" /></svg>
                                </span>
                                <span class="text-[#94A3B8] transition group-hover:text-[#2563EB]">→</span>
                            </div>
                            <p class="mt-4 font-bold text-[#172B4D]">{{ $folder['name'] }}</p>
                            <p class="mt-1 text-xs text-[#64748B]">{{ $folder['count'] }} file{{ $folder['count'] === 1 ? '' : 's' }}</p>
                        </a>
                    @endforeach
                </div>
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
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Anggaran</dt><dd class="mt-1 font-semibold text-[#0A2342]">{{ $proyek->anggaran !== null ? 'Rp '.number_format((float) $proyek->anggaran, 0, ',', '.') : 'Belum diisi' }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Status</dt><dd class="mt-1 text-sm font-semibold text-[#0F766E]">{{ $proyek->status }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Jenis Pekerjaan</dt><dd class="mt-1 text-sm font-semibold text-[#0A2342]">{{ $proyek->project_type ?: 'Belum diatur' }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Nama Kegiatan</dt><dd class="mt-1 text-sm text-[#43546A]">{{ $proyek->nama_kegiatan ?: 'Belum diisi' }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Durasi</dt><dd class="mt-1 text-sm text-[#43546A]">{{ $proyek->tanggal_mulai && $proyek->tanggal_selesai ? ($proyek->tanggal_mulai->diffInDays($proyek->tanggal_selesai) + 1).' hari' : ($proyek->waktu_pengerjaan ? $proyek->waktu_pengerjaan.' hari' : 'Belum ditentukan') }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Kontrak</dt><dd class="mt-1 text-sm text-[#43546A]">{{ $proyek->nama_kontrak ?: 'Belum diisi' }}{{ $proyek->nilai_kontrak !== null ? ' · Rp '.number_format((float) $proyek->nilai_kontrak, 0, ',', '.') : '' }}</dd></div>
                    @if ($proyek->drive_url)<div><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Dokumentasi Drive</dt><dd class="mt-1"><a href="{{ $proyek->drive_url }}" target="_blank" rel="noopener" class="text-sm font-semibold text-[#2563EB]">Buka Drive</a></dd></div>@endif
                    <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Deskripsi</dt><dd class="mt-1 whitespace-pre-line text-sm leading-6 text-[#43546A]">{{ $proyek->deskripsi ?: 'Deskripsi belum diisi.' }}</dd></div>
                </dl>
            </div>
            <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-6">
                <h3 class="font-bold text-[#0A2342]">Akses Modul Terkait</h3>
                <p class="mt-1 text-sm text-[#5B6B80]">Informasi berikut terhubung ke data proyek yang sama.</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    <a href="{{ route('contract.index', ['project_id' => $proyek->id]) }}" class="rounded-xl border border-[#E3E8EF] p-4 transition hover:border-[#14B8A6] hover:bg-[#F8FAFC]">
                        <span class="block text-xs text-[#718096]">Kontrak</span>
                        <span class="mt-1 block font-semibold text-[#0A2342]">{{ $proyek->contracts->count() }} kontrak terkait</span>
                    </a>
                    <a href="{{ route('project-assignment.index', ['project_id' => $proyek->id]) }}" class="rounded-xl border border-[#E3E8EF] p-4 transition hover:border-[#14B8A6] hover:bg-[#F8FAFC]">
                        <span class="block text-xs text-[#718096]">Penugasan</span>
                        <span class="mt-1 block font-semibold text-[#0A2342]">{{ $proyek->projectAssignments->count() }} penugasan terkait</span>
                    </a>
                    <a href="{{ route('dokumen-proyek.index', ['proyek_id' => $proyek->id]) }}" class="rounded-xl border border-[#E3E8EF] p-4 transition hover:border-[#14B8A6] hover:bg-[#F8FAFC]">
                        <span class="block text-xs text-[#718096]">Dokumen</span>
                        <span class="mt-1 block font-semibold text-[#0A2342]">{{ $proyek->dokumenProyek->count() }} dokumen terkait</span>
                    </a>
                    <a href="{{ route('progress-proyek.index', ['project_id' => $proyek->id]) }}" class="rounded-xl border border-[#E3E8EF] p-4 transition hover:border-[#14B8A6] hover:bg-[#F8FAFC]">
                        <span class="block text-xs text-[#718096]">Progress</span>
                        <span class="mt-1 block font-semibold text-[#0A2342]">{{ $proyek->progressProyek->count() }} laporan progress</span>
                    </a>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Kontrak Terkait</h3></div>
                <div class="divide-y divide-[#E3E8EF]">
                    @forelse ($proyek->contracts as $contract)
                        <a href="{{ route('contract.show', $contract) }}" class="block px-5 py-4 hover:bg-[#F8FAFC]">
                            <p class="font-semibold text-[#0A2342]">{{ $contract->contract_name }}</p>
                            <p class="mt-1 text-xs text-[#718096]">{{ $contract->contract_number }} · Rp {{ number_format((float) $contract->contract_value, 0, ',', '.') }}</p>
                        </a>
                    @empty
                        <p class="px-5 py-7 text-sm text-[#718096]">Belum ada kontrak untuk proyek ini.</p>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Penugasan Tim</h3></div>
                <div class="divide-y divide-[#E3E8EF]">
                    @forelse ($proyek->projectAssignments as $assignment)
                        <a href="{{ route('project-assignment.show', $assignment) }}" class="block px-5 py-4 hover:bg-[#F8FAFC]">
                            <p class="font-semibold text-[#0A2342]">{{ $assignment->user?->name ?? 'Pengguna tidak tersedia' }}</p>
                            <p class="mt-1 text-xs text-[#718096]">{{ $assignment->role_in_project ?: 'Peran belum diisi' }} · {{ \Carbon\Carbon::parse($assignment->assigned_date)->format('d M Y') }}</p>
                        </a>
                    @empty
                        <p class="px-5 py-7 text-sm text-[#718096]">Belum ada penugasan untuk proyek ini.</p>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Dokumen Proyek</h3></div>
                <div class="divide-y divide-[#E3E8EF]">
                    @forelse ($proyek->dokumenProyek as $dokumen)
                        <a href="{{ route('dokumen-proyek.show', $dokumen) }}" class="block px-5 py-4 hover:bg-[#F8FAFC]">
                            <p class="font-semibold text-[#0A2342]">{{ $dokumen->nama_dokumen }}</p>
                            <p class="mt-1 text-xs text-[#718096]">{{ $dokumen->kategoriDokumen?->nama_kategori ?? 'Kategori dokumen' }}</p>
                        </a>
                    @empty
                        <p class="px-5 py-7 text-sm text-[#718096]">Belum ada dokumen untuk proyek ini.</p>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <div class="border-b border-[#E3E8EF] px-5 py-4"><h3 class="font-bold text-[#0A2342]">Laporan Progress</h3></div>
                <div class="divide-y divide-[#E3E8EF]">
                    @forelse ($proyek->progressProyek as $progress)
                        <a href="{{ route('progress-proyek.show', $progress) }}" class="block px-5 py-4 hover:bg-[#F8FAFC]">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-semibold text-[#0A2342]">{{ $progress->tanggal_laporan?->format('d M Y') }}</p>
                                <span class="text-sm font-bold text-[#0F766E]">{{ $progress->persentase_progress }}%</span>
                            </div>
                            <p class="mt-1 line-clamp-2 text-sm text-[#5B6B80]">{{ $progress->uraian_pekerjaan }}</p>
                        </a>
                    @empty
                        <p class="px-5 py-7 text-sm text-[#718096]">Belum ada laporan progress.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
