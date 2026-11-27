<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-[#2563EB]">{{ $weekly->proyek->nama_proyek }}</p>
            <h2 class="text-2xl font-bold text-[#0A2342]">Minggu - {{ $weekly->week_number }}</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('weekly-documentation.photos.update', $weekly) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($weekly->photos->where('is_mandatory', true) as $photo)
                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <p class="mb-3 font-semibold text-[#0A2342]">Foto wajib {{ $photo->slot_index }}</p>
                        @if ($photo->path)
                            <img src="{{ route('weekly-documentation.photos.view', $photo) }}" alt="{{ $photo->description ?: 'Foto wajib' }}" class="mb-3 h-40 w-full rounded-xl object-cover">
                        @endif
                        <input type="file" name="photos[{{ $photo->id }}][image]" accept="image/*" class="w-full text-sm">
                        <input name="photos[{{ $photo->id }}][description]" value="{{ $photo->description }}" placeholder="Deskripsi foto" class="mt-3 w-full rounded-xl border-[#D7E0EA]">
                    </div>
                @endforeach
            </div>

            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-bold text-[#0A2342]">Detail tambahan</h3>
                    <button type="button" onclick="addDocumentationDetail()" class="rounded-xl border border-[#D7E0EA] px-3 py-2 text-sm font-semibold text-[#2563EB]">+ Tambah Detail</button>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($weekly->photos->where('is_mandatory', false) as $photo)
                        <div class="rounded-2xl bg-white p-4 shadow-sm">
                            @if ($photo->path)
                                <img src="{{ route('weekly-documentation.photos.view', $photo) }}" alt="{{ $photo->description ?: 'Foto tambahan' }}" class="mb-3 h-40 w-full rounded-xl object-cover">
                            @endif
                            <input name="photos[{{ $photo->id }}][description]" value="{{ $photo->description }}" required maxlength="255" class="w-full rounded-xl border-[#D7E0EA]" aria-label="Deskripsi foto tambahan">
                            <input type="file" name="photos[{{ $photo->id }}][image]" accept="image/*" class="mt-3 w-full text-sm">
                            <button type="submit" formaction="{{ route('weekly-documentation.photos.destroy', $photo) }}" formmethod="POST" name="_method" value="DELETE" class="mt-3 text-xs font-semibold text-red-600">Hapus foto</button>
                        </div>
                    @endforeach
                </div>
                <div id="documentation-details" class="mt-3 space-y-3"></div>
            </div>

            <button class="rounded-xl bg-[#2563EB] px-5 py-3 font-semibold text-white">Simpan Dokumentasi</button>
        </form>
    </div>

    <script>
        let detailIndex = 0;

        function addDocumentationDetail() {
            const row = document.createElement('div');
            row.className = 'grid gap-3 rounded-xl border border-[#E3E8EF] bg-white p-4 sm:grid-cols-[1fr_1fr_auto]';
            row.innerHTML = '<input type="file" name="details[' + detailIndex + '][image]" accept="image/*" required class="text-sm"><input name="details[' + detailIndex + '][description]" placeholder="Deskripsi detail" class="rounded-xl border-[#D7E0EA]"><button type="button" onclick="this.parentElement.remove()" class="text-sm font-semibold text-red-600">Hapus</button>';
            document.getElementById('documentation-details').appendChild(row);
            detailIndex++;
        }
    </script>
</x-app-layout>
