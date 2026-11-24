<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-[#0F766E]">Data Induk Proyek</p>
            <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Registrasi Proyek Baru</h2>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <form action="{{ route('proyek.store') }}" method="POST" enctype="multipart/form-data" class="mx-auto max-w-5xl space-y-6">
            @csrf
            <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-7">
                <div class="mb-6 border-b border-[#E3E8EF] pb-4">
                    <h3 class="font-bold text-[#0A2342]">Identitas Proyek</h3>
                    <p class="mt-1 text-sm text-[#5B6B80]">Kode proyek dapat dikosongkan untuk dibuat secara otomatis.</p>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <x-input-label for="kode_proyek" value="Kode Proyek (opsional)" />
                        <x-text-input id="kode_proyek" name="kode_proyek" type="text" maxlength="30" placeholder="Biarkan kosong untuk otomatis" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('kode_proyek') }}" />
                        <x-input-error :messages="$errors->get('kode_proyek')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status Proyek" />
                        <select id="status" name="status" required class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                            @foreach (['Perencanaan', 'Proses', 'Selesai', 'Ditunda', 'Batal'] as $status)
                                <option value="{{ $status }}" @selected(old('status', 'Perencanaan') === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="nama_proyek" value="Nama Proyek" />
                        <x-text-input id="nama_proyek" name="nama_proyek" type="text" required autofocus maxlength="150" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('nama_proyek') }}" />
                        <x-input-error :messages="$errors->get('nama_proyek')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="klien_id" value="Klien" />
                        <select id="klien_id" name="klien_id" required class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                            <option value="">Pilih klien</option>
                            @foreach ($klien as $item)
                                <option value="{{ $item->id }}" @selected((string) old('klien_id', $selectedKlienId ?? '') === (string) $item->id)>{{ $item->nama_klien }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('klien_id')" class="mt-2" />
                        @if ($klien->isEmpty())
                            <p class="mt-2 text-xs text-amber-700">Tambahkan data klien terlebih dahulu sebelum mendaftarkan proyek.</p>
                        @endif
                    </div>
                    <div>
                        <x-input-label for="jenis_pekerjaan_id" value="Jenis Pekerjaan" />
                        <select id="jenis_pekerjaan_id" name="jenis_pekerjaan_id" required class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                            <option value="">Pilih jenis pekerjaan</option>
                            @foreach ($jenisPekerjaan as $item)
                                <option value="{{ $item->id }}" @selected((string) old('jenis_pekerjaan_id', $selectedJenisPekerjaanId ?? '') === (string) $item->id)>{{ $item->nama_pekerjaan }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('jenis_pekerjaan_id')" class="mt-2" />
                        @if ($jenisPekerjaan->isEmpty())
                            <p class="mt-2 text-xs text-amber-700">Tambahkan jenis pekerjaan terlebih dahulu sebelum mendaftarkan proyek.</p>
                        @endif
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-7">
                <div class="mb-6 border-b border-[#E3E8EF] pb-4">
                    <h3 class="font-bold text-[#0A2342]">Jadwal, Anggaran & Foto Sampul</h3>
                    <p class="mt-1 text-sm text-[#5B6B80]">Target penyelesaian, anggaran, dan foto proyek dapat diperbarui kemudian.</p>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <x-input-label for="tanggal_mulai" value="Tanggal Mulai" />
                        <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" required class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('tanggal_mulai') }}" />
                        <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="tanggal_selesai" value="Target Penyelesaian" />
                        <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('tanggal_selesai') }}" />
                        <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="anggaran" value="Anggaran Proyek (Rp)" />
                        <x-text-input id="anggaran" name="anggaran" type="number" min="0" step="0.01" inputmode="decimal" class="mt-1 block min-h-11 w-full rounded-xl border-[#D7DFE8] focus:border-[#14B8A6] focus:ring-[#14B8A6]" value="{{ old('anggaran') }}" />
                        <x-input-error :messages="$errors->get('anggaran')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="cover_image" value="Foto Sampul Proyek (opsional)" />
                        <input id="cover_image" name="cover_image" type="file" accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full rounded-xl border border-[#D7DFE8] p-2 text-sm text-[#43546A] file:mr-4 file:rounded-lg file:border-0 file:bg-[#EEF3F8] file:px-4 file:py-2 file:text-xs file:font-semibold file:text-[#0A2342] hover:file:bg-[#E2E8F0]" />
                        <p class="mt-1 text-xs text-[#718096]">JPG, PNG, atau WEBP. Maksimal 5 MB.</p>
                        <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="deskripsi" value="Deskripsi Proyek" />
                        <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">{{ old('deskripsi') }}</textarea>
                        <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                    </div>
                </div>
            </section>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('proyek.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#D7DFE8] px-5 text-sm font-semibold text-[#43546A] hover:bg-[#F5F8FA]">Batal</a>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0F766E] px-5 text-sm font-semibold text-white hover:bg-[#115E59]">Simpan Proyek</button>
            </div>
        </form>
    </div>
</x-app-layout>
