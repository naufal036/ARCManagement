<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-[#2563EB]">Workspace pribadi</p>
                <h2 class="text-2xl font-bold text-[#0A2342]">My Reports</h2>
            </div>
            <button type="button" onclick="document.getElementById('report-form').classList.toggle('hidden')" class="rounded-xl bg-[#2563EB] px-4 py-2 text-sm font-semibold text-white">+ Report</button>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <form id="report-form" method="POST" action="{{ route('reports.store') }}" class="hidden grid gap-4 rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-sm md:grid-cols-2">
            @csrf
            <input name="title" required placeholder="Judul report / tugas" class="rounded-xl border-[#D7E0EA]">
            <select name="proyek_id" class="rounded-xl border-[#D7E0EA]"><option value="">Tanpa proyek</option>@foreach ($projects as $project)<option value="{{ $project->id }}">{{ $project->nama_proyek }}</option>@endforeach</select>
            <select name="status" class="rounded-xl border-[#D7E0EA]">@foreach (array_slice($statuses, 1) as $status)<option>{{ $status }}</option>@endforeach</select>
            <select name="priority" class="rounded-xl border-[#D7E0EA]">@foreach (['Prioritas I', 'Prioritas II', 'Prioritas III', 'High Urgent', 'Low Urgent'] as $priority)<option>{{ $priority }}</option>@endforeach</select>
            <input type="date" name="due_date" class="rounded-xl border-[#D7E0EA]">
            <textarea name="notes" placeholder="Catatan (opsional)" class="rounded-xl border-[#D7E0EA] md:col-span-2"></textarea>
            <button class="rounded-xl bg-[#0A2342] px-4 py-2 font-semibold text-white md:col-span-2">Simpan Report</button>
        </form>

        <div class="flex gap-2 overflow-x-auto">
            @foreach ($statuses as $status)
                <a href="{{ route('reports.index', ['status' => $status, 'month' => $month->format('Y-m')]) }}" class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold {{ $selectedStatus === $status ? 'bg-[#0A2342] text-white' : 'bg-white text-[#5B6B80] ring-1 ring-[#E3E8EF]' }}">{{ $status }}</a>
            @endforeach
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($reports as $report)
                <article class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="font-bold text-[#0A2342]">{{ $report->title }}</h3>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $report->status === 'Completed' ? 'bg-blue-100 text-blue-700' : ($report->status === 'On Review' ? 'bg-yellow-100 text-yellow-700' : ($report->status === 'Need Changes' ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700')) }}">{{ $report->status }}</span>
                    </div>
                    <p class="mt-3 text-sm text-[#5B6B80]">{{ $report->proyek?->nama_proyek ?? 'Tanpa proyek' }}</p>
                    <p class="mt-2 text-xs text-[#64748B]">{{ $report->due_date?->format('d M Y') ?? 'Tanpa deadline' }} · {{ $report->priority }}</p>
                    <p class="mt-3 text-sm text-[#5B6B80]">{{ $report->notes }}</p>
                    <details class="mt-4">
                        <summary class="cursor-pointer text-xs font-semibold text-[#2563EB]">Edit</summary>
                        <form method="POST" action="{{ route('reports.update', $report) }}" class="mt-3 space-y-2">
                            @csrf
                            @method('PUT')
                            <input name="title" value="{{ $report->title }}" required class="w-full rounded-xl border-[#D7E0EA] text-sm">
                            <select name="proyek_id" class="w-full rounded-xl border-[#D7E0EA] text-sm">
                                <option value="">Tanpa proyek</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" @selected($report->proyek_id === $project->id)>{{ $project->nama_proyek }}</option>
                                @endforeach
                            </select>
                            <select name="status" class="w-full rounded-xl border-[#D7E0EA] text-sm">@foreach (array_slice($statuses, 1) as $status)<option @selected($report->status === $status)>{{ $status }}</option>@endforeach</select>
                            <select name="priority" class="w-full rounded-xl border-[#D7E0EA] text-sm">@foreach (['Prioritas I', 'Prioritas II', 'Prioritas III', 'High Urgent', 'Low Urgent'] as $priority)<option @selected($report->priority === $priority)>{{ $priority }}</option>@endforeach</select>
                            <input type="date" name="due_date" value="{{ $report->due_date?->format('Y-m-d') }}" class="w-full rounded-xl border-[#D7E0EA] text-sm">
                            <textarea name="notes" class="w-full rounded-xl border-[#D7E0EA] text-sm">{{ $report->notes }}</textarea>
                            <button class="rounded-lg bg-[#0A2342] px-3 py-2 text-xs font-semibold text-white">Simpan</button>
                        </form>
                    </details>
                    <form method="POST" action="{{ route('reports.destroy', $report) }}" class="mt-4">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs font-semibold text-red-600">Hapus</button>
                    </form>
                </article>
            @empty
                <p class="rounded-2xl bg-white p-8 text-center text-sm text-[#5B6B80] md:col-span-3">Belum ada report pada filter ini.</p>
            @endforelse
        </div>

        <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div><h3 class="font-bold text-[#0A2342]">Calendar Reminder</h3><p class="text-sm text-[#64748B]">Deadline report Anda untuk {{ $month->format('F Y') }}.</p></div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.index', ['status' => $selectedStatus, 'month' => $month->subMonth()->format('Y-m')]) }}" class="rounded-lg border border-[#D7E0EA] px-3 py-2 text-sm" aria-label="Bulan sebelumnya">‹</a>
                    <a href="{{ route('reports.index', ['status' => $selectedStatus, 'month' => $month->addMonth()->format('Y-m')]) }}" class="rounded-lg border border-[#D7E0EA] px-3 py-2 text-sm" aria-label="Bulan berikutnya">›</a>
                </div>
            </div>
            <div class="mt-4 overflow-x-auto">
                <div class="grid min-w-[620px] grid-cols-7 gap-2 text-center text-xs">
                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
                        <div class="font-semibold text-[#64748B]">{{ $weekday }}</div>
                    @endforeach
                    @for ($blank = 0; $blank < $leadingDays; $blank++)
                        <div class="min-h-20 rounded-xl bg-[#F8FAFC]"></div>
                    @endfor
                    @foreach ($calendarDays as $calendarDay)
                        <div class="min-h-20 rounded-xl border border-[#E3E8EF] p-2 text-left">
                            <span class="font-semibold text-[#0A2342]">{{ $calendarDay->day }}</span>
                            @foreach ($reports->filter(fn ($calendarReport): bool => $calendarReport->due_date?->isSameDay($calendarDay)) as $calendarReport)
                                <p class="mt-2 truncate rounded bg-blue-50 px-1 py-0.5 text-[10px] text-blue-700" title="{{ $calendarReport->title }}">{{ $calendarReport->title }}</p>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
