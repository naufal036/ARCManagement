<x-app-layout>
    <x-slot name="header"><div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm font-medium text-[#2563EB]">{{ $dokumenProyek->kategoriDokumen?->nama_kategori }}</p><h2 class="text-2xl font-bold tracking-tight text-[#172B4D]">{{ $dokumenProyek->nama_dokumen }}</h2></div><div class="flex gap-2"><a href="{{ route('dokumen-proyek.index') }}" class="rounded-xl border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-semibold text-[#64748B]">Kembali</a><a href="{{ route('dokumen-proyek.edit', $dokumenProyek) }}" class="rounded-xl bg-[#172B4D] px-4 py-2.5 text-sm font-semibold text-white">Edit</a></div></div></x-slot>
    <div class="space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <section class="rounded-2xl border border-[#E3E8EF] bg-white p-6 shadow-[0_8px_24px_rgba(15,27,45,0.05)]"><div class="mb-5 flex flex-wrap items-center justify-between gap-3"><div><p class="text-xs font-semibold uppercase tracking-wide text-[#8A97A8]">Nomor dokumen</p><p class="mt-1 font-mono text-sm font-semibold text-[#2A6F97]">{{ $dokumenProyek->nomor_dokumen }}</p></div><span class="rounded-full bg-[#E8EEF5] px-3 py-1 text-sm font-semibold text-[#0A2342]">{{ $dokumenProyek->status_verifikasi->label() }}</span></div><dl class="grid gap-5 sm:grid-cols-3"><div><dt class="text-xs font-semibold uppercase tracking-wide text-[#8A97A8]">Proyek</dt><dd class="mt-1 font-semibold text-[#0F1B2D]">{{ $dokumenProyek->proyek?->nama_proyek }}</dd></div><div><dt class="text-xs font-semibold uppercase tracking-wide text-[#8A97A8]">Kategori</dt><dd class="mt-1 text-[#5B6B80]">{{ $dokumenProyek->kategoriDokumen?->nama_kategori }}</dd></div><div><dt class="text-xs font-semibold uppercase tracking-wide text-[#8A97A8]">Deskripsi</dt><dd class="mt-1 text-[#5B6B80]">{{ $dokumenProyek->deskripsi ?: '-' }}</dd></div><div><dt class="text-xs font-semibold uppercase tracking-wide text-[#8A97A8]">Siklus pemeriksaan</dt><dd class="mt-1 text-[#5B6B80]">{{ $dokumenProyek->verification_cycle }}</dd></div></dl></section>
        @can('submitForReview', $dokumenProyek)
            <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5">
                <h3 class="font-bold text-[#0A2342]">Ajukan untuk pemeriksaan</h3>
                <p class="mt-1 text-sm text-[#5B6B80]">Pengajuan memerlukan setidaknya satu versi file.</p>
                <form method="POST" action="{{ route('dokumen-proyek.workflow.submit', $dokumenProyek) }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="lock_version" value="{{ $dokumenProyek->lock_version }}">
                    <button class="rounded-xl bg-[#0A2342] px-4 py-2.5 text-sm font-semibold text-white">Ajukan Dokumen</button>
                </form>
            </section>
        @endcan
        @can('review', $dokumenProyek)
            <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5">
                <h3 class="font-bold text-[#0A2342]">Pemeriksaan Pengawas Lapangan</h3>
                <form method="POST" action="{{ route('dokumen-proyek.workflow.review', $dokumenProyek) }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="lock_version" value="{{ $dokumenProyek->lock_version }}">
                    <label class="text-sm font-semibold text-[#5B6B80]">Hasil pemeriksaan
                        <select name="decision" class="mt-1 w-full rounded-xl border-[#D7E0EA]" required>
                            <option value="pass">Lolos ke keputusan Direktur</option>
                            <option value="revision_required">Perlu revisi</option>
                            <option value="rejected">Tolak</option>
                        </select>
                    </label>
                    <label class="text-sm font-semibold text-[#5B6B80]">Catatan
                        <textarea name="note" rows="2" class="mt-1 w-full rounded-xl border-[#D7E0EA]" placeholder="Wajib untuk revisi atau penolakan"></textarea>
                    </label>
                    <button class="rounded-xl bg-[#0A2342] px-4 py-2.5 text-sm font-semibold text-white sm:col-span-2">Simpan Hasil Pemeriksaan</button>
                </form>
            </section>
        @endcan
        @can('finalize', $dokumenProyek)
            @if ($hasCurrentReviewPass)
                <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5">
                    <h3 class="font-bold text-[#0A2342]">Keputusan akhir Direktur</h3>
                    <form method="POST" action="{{ route('dokumen-proyek.workflow.finalize', $dokumenProyek) }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                        @csrf
                        <input type="hidden" name="lock_version" value="{{ $dokumenProyek->lock_version }}">
                        <label class="text-sm font-semibold text-[#5B6B80]">Keputusan
                            <select name="decision" class="mt-1 w-full rounded-xl border-[#D7E0EA]" required>
                                <option value="approved">Setujui</option>
                                <option value="revision_required">Perlu revisi</option>
                                <option value="rejected">Tolak</option>
                            </select>
                        </label>
                        <label class="text-sm font-semibold text-[#5B6B80]">Catatan
                            <textarea name="note" rows="2" class="mt-1 w-full rounded-xl border-[#D7E0EA]" placeholder="Wajib untuk revisi atau penolakan"></textarea>
                        </label>
                        <button class="rounded-xl bg-[#0A2342] px-4 py-2.5 text-sm font-semibold text-white sm:col-span-2">Simpan Keputusan Akhir</button>
                    </form>
                </section>
            @endif
        @endcan
        <section class="rounded-2xl border border-[#E3E8EF] bg-white p-6 shadow-[0_8px_24px_rgba(15,27,45,0.05)]"><h3 class="font-bold text-[#0A2342]">Upload versi baru</h3><p class="mt-1 text-sm text-[#5B6B80]">Maksimal 20 file, 50 MB per file, dan total 100 MB per batch. File disimpan privat.</p><form method="POST" action="{{ route('dokumen-proyek.versi.store', $dokumenProyek) }}" enctype="multipart/form-data" class="mt-5 space-y-4">@csrf<input type="file" name="files[]" multiple required accept=".pdf,.dwg,.dxf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.zip,.rar,.skp,.ifc" class="block w-full rounded-xl border border-[#D7E0EA] p-2 text-sm"><textarea name="catatan" rows="2" placeholder="Catatan revisi (opsional)" class="w-full rounded-xl border-[#D7E0EA] text-sm">{{ old('catatan') }}</textarea><button class="rounded-xl bg-[#0A2342] px-4 py-2.5 text-sm font-semibold text-white">Unggah File</button>@error('files')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</form></section>
        <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            <div class="border-b border-[#E3E8EF] px-6 py-5">
                <h3 class="font-bold text-[#0A2342]">Riwayat dan perbandingan versi</h3>
                <p class="mt-1 text-sm text-[#5B6B80]">Versi lama tetap tersimpan; bandingkan nama file, catatan, waktu, pengunggah, dan checksum.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="bg-[#F8FAFC] text-xs uppercase tracking-wide text-[#5B6B80]">
                        <tr><th class="px-6 py-3">Versi</th><th class="px-6 py-3">File dan checksum</th><th class="px-6 py-3">Pengunggah</th><th class="px-6 py-3">Waktu revisi</th><th class="px-6 py-3">Catatan</th><th class="px-6 py-3 text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3E8EF]">
                        @forelse ($dokumenProyek->versiDokumen as $versi)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 font-semibold text-[#0F1B2D]">v{{ $versi->nomor_versi }} @if ((int) $versi->nomor_versi === (int) $dokumenProyek->versiTerbaru?->nomor_versi)<span class="rounded-full bg-emerald-50 px-2 py-1 text-xs text-emerald-700">Terbaru</span>@endif</td>
                                <td class="px-6 py-4 text-[#5B6B80]"><p>{{ $versi->nama_file_asli }} ({{ number_format($versi->ukuran_file / 1024, 1) }} KB)</p><p class="mt-1 font-mono text-xs text-[#8A97A8]">{{ $versi->checksum_sha256 }}</p></td>
                                <td class="px-6 py-4 text-[#5B6B80]">{{ $versi->pengunggah?->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-[#5B6B80]">{{ $versi->created_at?->format('d M Y H:i:s') ?? '-' }}</td>
                                <td class="px-6 py-4 text-[#5B6B80]">{{ $versi->catatan ?: '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    @if (in_array($versi->mime_type, ['application/pdf', 'image/png', 'image/jpeg'], true))
                                        <a target="_blank" rel="noopener" href="{{ route('versi-dokumen.view', $versi) }}" class="font-semibold text-[#2A6F97]">Preview</a>
                                    @endif
                                    <a href="{{ route('versi-dokumen.download', $versi) }}" class="ml-3 font-semibold text-[#5B6B80]">Download</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-[#5B6B80]">Belum ada versi dokumen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <section class="rounded-2xl border border-[#E3E8EF] bg-white p-6 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            <h3 class="font-bold text-[#0A2342]">Audit trail dokumen</h3>
            <div class="mt-4 space-y-4">
                @forelse ($dokumenProyek->activityLogs as $activity)
                    <article class="border-s-2 border-[#2A6F97] ps-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-semibold text-[#0F1B2D]">{{ str_replace('_', ' ', $activity->activity) }}</p>
                            <time class="text-xs text-[#8A97A8]">{{ $activity->created_at?->format('d M Y H:i:s') }}</time>
                        </div>
                        <p class="mt-1 text-sm text-[#5B6B80]">{{ $activity->description ?: '-' }} · {{ $activity->user?->name ?? 'Pengguna dihapus' }}</p>
                    </article>
                @empty
                    <p class="text-sm text-[#5B6B80]">Belum ada aktivitas tercatat.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
