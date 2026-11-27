<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Tambah Dokumen Proyek</h2></x-slot>
    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('dokumen-proyek.store') }}" enctype="multipart/form-data" class="mx-auto max-w-3xl space-y-5 rounded-2xl border border-[#E3E8EF] bg-white p-6 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            @csrf
            @if ($errors->any())
                <div role="alert" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif
            <div class="grid gap-5 sm:grid-cols-2">
                <div><label for="nomor_dokumen" class="block text-sm font-semibold text-[#0F1B2D]">Nomor Dokumen</label><input id="nomor_dokumen" name="nomor_dokumen" value="{{ old('nomor_dokumen') }}" required class="mt-1 w-full rounded-xl border-[#D7E0EA]">@error('nomor_dokumen')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="nama_dokumen" class="block text-sm font-semibold text-[#0F1B2D]">Nama Dokumen</label><input id="nama_dokumen" name="nama_dokumen" value="{{ old('nama_dokumen') }}" required class="mt-1 w-full rounded-xl border-[#D7E0EA]">@error('nama_dokumen')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
            <div><label for="proyek_id" class="block text-sm font-semibold text-[#0F1B2D]">Proyek</label><select id="proyek_id" name="proyek_id" required class="mt-1 w-full rounded-xl border-[#D7E0EA]"><option value="">Pilih proyek</option>@foreach ($proyeks as $proyek)<option value="{{ $proyek->id }}" @selected(old('proyek_id') == $proyek->id)>{{ $proyek->nama_proyek }} · {{ $proyek->klien?->nama_klien ?? 'Client belum diatur' }}</option>@endforeach</select></div>
            <div><label for="deskripsi" class="block text-sm font-semibold text-[#0F1B2D]">Deskripsi</label><textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 w-full rounded-xl border-[#D7E0EA]">{{ old('deskripsi') }}</textarea></div>
            <div><label for="drive_url" class="block text-sm font-semibold text-[#0F1B2D]">Drive URL <span class="font-normal text-[#8A97A8]">(opsional)</span></label><input id="drive_url" type="url" name="drive_url" value="{{ old('drive_url') }}" placeholder="https://drive.google.com/..." class="mt-1 w-full rounded-xl border-[#D7E0EA]">@error('drive_url')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div>
                <label for="files" class="block text-sm font-semibold text-[#0F1B2D]">Upload File <span class="text-red-600">*</span></label>
                <label for="files" class="mt-1 flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#B7C6D8] bg-[#F8FAFC] px-5 py-8 text-center hover:border-[#2563EB]">
                    <span class="font-semibold text-[#0A2342]">Tarik file ke sini atau pilih file</span>
                    <span class="mt-1 text-xs text-[#64748B]">Maksimal 20 file, total 100 MB, satu file maksimal 50 MB</span>
                    <span id="file-list" class="mt-3 text-sm text-[#2563EB]"></span>
                </label>
                <input id="files" name="files[]" type="file" multiple required class="sr-only" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.dwg,.dxf,.ifc,.skp,.jpg,.jpeg,.png,.zip,.rar,.ppt,.pptx">
                @error('files')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3"><button class="rounded-xl bg-[#0A2342] px-4 py-2.5 text-sm font-semibold text-white">Simpan Dokumen</button><a href="{{ route('dokumen-proyek.index') }}" class="rounded-xl border border-[#D7E0EA] px-4 py-2.5 text-sm font-semibold text-[#5B6B80]">Batal</a></div>
        </form>
    </div>
    <script>
        document.getElementById('files')?.addEventListener('change', function () {
            const files = Array.from(this.files);
            document.getElementById('file-list').textContent = files.length ? files.map(file => file.name + ' (' + Math.round(file.size / 1024) + ' KB)').join(', ') : '';
        });
    </script>
</x-app-layout>
