<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Data Proyek') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4">
                    <div class="text-sm text-indigo-700">
                        @switch(session('status'))
                            @case('proyek-created') {{ __('Data proyek berhasil ditambahkan.') }} @break
                            @case('proyek-updated') {{ __('Data proyek berhasil diperbarui.') }} @break
                            @case('proyek-deleted') {{ __('Data proyek berhasil dihapus.') }} @break
                        @endswitch
                    </div>
                </div>
            @endif

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="text-sm font-semibold uppercase tracking-[0.14em] text-[#2563EB]">Project portfolio</p><h3 class="mt-1 text-2xl font-bold tracking-tight text-[#172B4D]">Projects</h3><p class="mt-1 text-sm text-[#64748B]">Kelola proyek konstruksi dan pantau progresnya.</p></div>
                <div>
                    <a href="{{ route('proyek.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#2563EB] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1D4ED8]">+ Add New Project</a>
                </div>
            </div>

            <form method="GET" class="grid gap-3 rounded-2xl border border-[#E2E8F0] bg-white p-4 shadow-[0_8px_24px_rgba(23,43,77,0.05)] md:grid-cols-[1fr_180px_150px_auto] md:items-end">
                <div><label for="project-search" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Search project</label><input id="project-search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, code, or client" class="mt-1 w-full rounded-xl border-[#E2E8F0] text-sm focus:border-[#2563EB] focus:ring-[#2563EB]"></div>
                <div><label for="project-status" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Status</label><select id="project-status" name="status" class="mt-1 w-full rounded-xl border-[#E2E8F0] text-sm focus:border-[#2563EB] focus:ring-[#2563EB]"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>@endforeach</select></div>
                <div><label for="project-year" class="block text-xs font-semibold uppercase tracking-wide text-[#64748B]">Year</label><select id="project-year" name="year" class="mt-1 w-full rounded-xl border-[#E2E8F0] text-sm focus:border-[#2563EB] focus:ring-[#2563EB]"><option value="">All years</option>@foreach ($years as $year)<option value="{{ $year }}" @selected((string) ($filters['year'] ?? '') === (string) $year)>{{ $year }}</option>@endforeach</select></div>
                <button type="submit" class="rounded-xl bg-[#172B4D] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#243B5A]">Filter</button>
            </form>

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($proyek as $item)
                    @php $progress = min((int) ($item->progress_proyek_max_persentase_progress ?? 0), 100); @endphp
                    <article class="group overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-[0_8px_24px_rgba(23,43,77,0.05)] transition hover:-translate-y-1 hover:shadow-[0_14px_30px_rgba(23,43,77,0.1)]">
                        <div class="relative h-36 overflow-hidden bg-[#172B4D]">@if ($item->cover_image_path)<img src="{{ asset('storage/'.$item->cover_image_path) }}" alt="Foto sampul {{ $item->nama_proyek }}" class="absolute inset-0 h-full w-full object-cover"><div class="absolute inset-0 bg-[#172B4D]/65"></div>@else<div class="absolute inset-0 opacity-30" style="background-image: linear-gradient(135deg, transparent 0 38%, #2563EB 38% 42%, transparent 42% 100%), linear-gradient(45deg, transparent 0 56%, #60A5FA 56% 59%, transparent 59% 100%);"></div>@endif<div class="absolute bottom-4 left-5 flex items-center gap-2"><span class="rounded-lg bg-white/15 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider text-blue-100">{{ $item->kode_proyek }}</span><span class="rounded-lg bg-[#2563EB] px-2.5 py-1 text-[11px] font-semibold text-white">{{ $item->status }}</span></div></div>
                        <div class="p-5"><h4 class="truncate text-lg font-bold text-[#172B4D]">{{ $item->nama_proyek }}</h4><p class="mt-2 flex items-center gap-2 text-sm text-[#64748B]"><svg class="h-4 w-4 text-[#2563EB]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" /><circle cx="12" cy="10" r="2.5" /></svg>{{ $item->klien?->nama_klien ?? 'Client belum diatur' }}</p><div class="mt-5 flex items-center justify-between text-xs font-semibold text-[#64748B]"><span>Progress</span><span class="text-[#2563EB]">{{ $progress }}%</span></div><div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#E2E8F0]"><div class="h-full rounded-full bg-[#2563EB]" style="width: {{ $progress }}%"></div></div><div class="mt-5 flex items-center justify-between"><span class="text-xs text-[#64748B]">{{ optional($item->tanggal_mulai)->format('Y') ?? '—' }} · {{ $item->jenisPekerjaan?->nama_pekerjaan ?? 'Project' }}</span><a href="{{ route('proyek.show', $item) }}" class="text-sm font-semibold text-[#2563EB] hover:text-[#172B4D]">View details <span aria-hidden="true">→</span></a></div></div>
                    </article>
                @empty
                    <div class="md:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-[#CBD5E1] bg-white px-6 py-14 text-center"><p class="font-semibold text-[#172B4D]">Belum ada project yang cocok.</p><p class="mt-1 text-sm text-[#64748B]">Coba ubah filter atau tambahkan project baru.</p></div>
                @endforelse
            </div>

            <div class="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-[0_8px_24px_rgba(23,43,77,0.05)]">
                <div class="px-4 py-5 sm:px-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Kode Proyek') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nama Proyek') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Klien') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Jenis Pekerjaan') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="relative px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($proyek as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->kode_proyek }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->nama_proyek }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($item->klien)->nama_klien ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($item->jenisPekerjaan)->nama_pekerjaan ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} @if ($item->tanggal_selesai) s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }} @endif</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex px-2 py-1 text-xs font-semibold leading-full rounded-full @if ($item->status == 'Selesai') bg-green-100 text-green-800 @elseif ($item->status == 'Proses') bg-blue-100 text-blue-800 @elseif ($item->status == 'Ditunda') bg-yellow-100 text-yellow-800 @elseif ($item->status == 'Batal') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif">{{ ucfirst($item->status) }}</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('proyek.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                        <form action="{{ route('proyek.destroy', $item) }}" method="POST" class="inline-block">@csrf @method('DELETE') <button type="submit" class="text-red-600 hover:text-red-900 ml-3" onclick="return confirm('{{ __('Apakah Anda yakin ingin menghapus proyek ini?') }}')">{{ __('Hapus') }}</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('Belum ada data proyek.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $proyek->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
