<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-[#0F766E]">Data Induk Proyek · {{ $proyek->kode_proyek }}</p>
            <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Edit Proyek</h2>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <form action="{{ route('proyek.update', $proyek) }}" method="POST" enctype="multipart/form-data" class="mx-auto max-w-5xl space-y-6">
            @csrf
            @method('PUT')
            <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-7">
                <div class="mb-6 border-b border-[#E3E8EF] pb-4">
                    <h3 class="font-bold text-[#0A2342]">Identitas Proyek</h3>
                    <p class="mt-1 text-sm text-[#5B6B80]">Data proyek ini juga digunakan oleh kontrak, penugasan, dokumen, dan progress.</p>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                        <div><x-input-label for="nama_kegiatan" value="Nama Kegiatan" /><x-text-input id="nama_kegiatan" name="nama_kegiatan" class="mt-1 block w-full" value="{{ old('nama_kegiatan', $proyek->nama_kegiatan) }}" /></div>
                        <div><x-input-label for="instansi" value="Instansi" /><x-text-input id="instansi" name="instansi" class="mt-1 block w-full" value="{{ old('instansi', $proyek->instansi) }}" /></div>
                        <div><x-input-label for="lokasi" value="Lokasi" /><x-text-input id="lokasi" name="lokasi" class="mt-1 block w-full" value="{{ old('lokasi', $proyek->lokasi) }}" /></div>
                        <div><x-input-label for="project_type" value="Jenis Pekerjaan" /><select id="project_type" name="project_type" required class="mt-1 block w-full rounded-xl border-[#D7DFE8]"><option value="Perencanaan" @selected(old('project_type', $proyek->project_type) === 'Perencanaan')>Perencanaan</option><option value="Pengawasan" @selected(old('project_type', $proyek->project_type) === 'Pengawasan')>Pengawasan</option></select></div>
                        <div><x-input-label for="waktu_pengerjaan" value="Waktu Pengerjaan (hari)" /><x-text-input id="waktu_pengerjaan" name="waktu_pengerjaan" type="number" min="1" class="mt-1 block w-full" value="{{ old('waktu_pengerjaan', $proyek->waktu_pengerjaan) }}" /></div>
                        <div><x-input-label for="nama_kontrak" value="Nama Kontrak" /><x-text-input id="nama_kontrak" name="nama_kontrak" class="mt-1 block w-full" value="{{ old('nama_kontrak', $proyek->nama_kontrak) }}" /></div>
                    <div>
                        <x-input-label for="kode_proyek" value="Kode Proyek" />
                        <x-text-input id="kode_proyek" name="kode_proyek" type="text" required maxlength="30" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('kode_proyek', $proyek->kode_proyek) }}" />
                        <x-input-error :messages="$errors->get('kode_proyek')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status Proyek" />
                        <select id="status" name="status" required class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                            @if (! in_array($proyek->status, ['Perencanaan', 'Proses', 'Selesai', 'Ditunda', 'Batal'], true))
                                <option value="{{ $proyek->status }}" selected>{{ $proyek->status }}</option>
                            @endif
                            @foreach (['Aktif', 'Selesai', 'Perencanaan', 'Proses', 'Ditunda', 'Batal'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $proyek->status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="nama_proyek" value="Nama Proyek" />
                        <x-text-input id="nama_proyek" name="nama_proyek" type="text" required maxlength="150" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('nama_proyek', $proyek->nama_proyek) }}" />
                        <x-input-error :messages="$errors->get('nama_proyek')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="klien_id" value="Klien" />
                        <select id="klien_id" name="klien_id" required class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                            @foreach ($klien as $item)
                                <option value="{{ $item->id }}" @selected((string) old('klien_id', $proyek->klien_id) === (string) $item->id)>{{ $item->nama_klien }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('klien_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="jenis_pekerjaan_id" value="Jenis Pekerjaan" />
                        <select id="jenis_pekerjaan_id" name="jenis_pekerjaan_id" required class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                            @foreach ($jenisPekerjaan as $item)
                                <option value="{{ $item->id }}" @selected((string) old('jenis_pekerjaan_id', $proyek->jenis_pekerjaan_id) === (string) $item->id)>{{ $item->nama_pekerjaan }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('jenis_pekerjaan_id')" class="mt-2" />
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-7">
                <div class="mb-6 border-b border-[#E3E8EF] pb-4">
                    <h3 class="font-bold text-[#0A2342]">Jadwal, Anggaran & Foto Sampul</h3>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <x-input-label for="tanggal_mulai" value="Tanggal Mulai" />
                        <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" required class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('tanggal_mulai', $proyek->tanggal_mulai?->format('Y-m-d')) }}" />
                        <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="tanggal_selesai" value="Target Penyelesaian" />
                        <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('tanggal_selesai', $proyek->tanggal_selesai?->format('Y-m-d')) }}" />
                        <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="anggaran" value="Anggaran Proyek (Rp)" />
                        <x-text-input id="anggaran" name="anggaran" type="number" min="0" step="0.01" inputmode="decimal" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('anggaran', $proyek->anggaran) }}" />
                        <x-input-error :messages="$errors->get('anggaran')" class="mt-2" />
                    </div>
                    <div><x-input-label for="nilai_kontrak" value="Nilai Kontrak (Rp)" /><x-text-input id="nilai_kontrak" name="nilai_kontrak" type="number" min="0" step="0.01" class="mt-1 block w-full" value="{{ old('nilai_kontrak', $proyek->nilai_kontrak) }}" /></div>
                    <div class="md:col-span-2"><x-input-label for="drive_url" value="Link Google Drive Dokumentasi (opsional)" /><x-text-input id="drive_url" name="drive_url" type="url" class="mt-1 block w-full" value="{{ old('drive_url', $proyek->drive_url) }}" /></div>
                    <div>
                        <x-input-label for="cover_image" value="Foto Sampul Proyek" />
                        @if ($proyek->cover_image_path)
                            <div class="mt-2 mb-3 flex items-center gap-4">
                                <img src="{{ asset('storage/'.$proyek->cover_image_path) }}" alt="Foto sampul {{ $proyek->nama_proyek }}" class="h-20 w-32 rounded-xl object-cover">
                                <button type="submit" form="remove-cover-form" class="text-xs font-semibold text-red-600 hover:text-red-800">Hapus foto sampul</button>
                            </div>
                        @endif
                        <input id="cover_image" name="cover_image" type="file" accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full rounded-xl border border-[#D7DFE8] p-2 text-sm text-[#43546A] file:mr-4 file:rounded-lg file:border-0 file:bg-[#EEF3F8] file:px-4 file:py-2 file:text-xs file:font-semibold file:text-[#0A2342] hover:file:bg-[#E2E8F0]" />
                        <p class="mt-1 text-xs text-[#718096]">Upload file baru untuk mengganti foto saat ini. Maksimal 5 MB.</p>
                        <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="deskripsi" value="Deskripsi Proyek" />
                        <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">{{ old('deskripsi', $proyek->deskripsi) }}</textarea>
                        <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                    </div>
                </div>
            </section>
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('proyek.show', $proyek) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#D7DFE8] px-5 text-sm font-semibold text-[#43546A] hover:bg-[#F5F8FA]">Batal</a>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0F766E] px-5 text-sm font-semibold text-white hover:bg-[#115E59]">Simpan Perubahan</button>
            </div>
        </form>

        @if ($proyek->cover_image_path)
            <form id="remove-cover-form" method="POST" action="{{ route('proyek.cover.destroy', $proyek) }}" onsubmit="return confirm('Hapus foto sampul proyek ini?');">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</x-app-layout>
