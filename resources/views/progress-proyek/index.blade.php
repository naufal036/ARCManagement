<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#2A6F97]">Kinerja proyek</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Progress Reporting</h2>
            </div>
            <a href="{{ route('progress-proyek.create') }}" class="inline-flex items-center justify-center rounded-xl bg-[#0A2342] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#123B63]">
                + Tambah Progress
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
        @endif

        <section class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#2A6F97]">Analisis progress</p>
                    <h3 class="mt-1 text-lg font-bold text-[#0A2342]">Kurva S Progress</h3>
                    <p class="mt-1 max-w-2xl text-sm text-[#5B6B80]">{{ $sCurve['baselineLabel'] }}</p>
                </div>
                <form method="GET" action="{{ route('progress-proyek.index') }}" class="flex w-full gap-2 sm:w-auto">
                    <label for="curve-project" class="sr-only">Pilih proyek</label>
                    <select id="curve-project" name="project_id" onchange="this.form.submit()" class="min-h-10 w-full rounded-xl border-[#D7E0EA] text-sm text-[#0F1B2D] shadow-sm focus:border-[#14B8A6] focus:ring-[#14B8A6] sm:w-72">
                        @forelse ($proyeks as $proyek)
                            <option value="{{ $proyek->id }}" @selected($selectedProject?->id === $proyek->id)>{{ $proyek->nama_proyek }}</option>
                        @empty
                            <option>Belum ada proyek</option>
                        @endforelse
                    </select>
                </form>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['label' => 'Minggu berjalan', 'value' => $sCurve['currentWeek'] > 0 ? 'Minggu '.$sCurve['currentWeek'] : 'Belum mulai', 'tone' => 'bg-[#E8EEF5] text-[#0A2342]'],
                    ['label' => 'Progress aktual', 'value' => number_format($sCurve['actualProgress'], 2).'%', 'tone' => 'bg-[#E6F4F3] text-[#0F766E]'],
                    ['label' => 'Deviasi rencana', 'value' => ($sCurve['deviation'] >= 0 ? '+' : '').number_format($sCurve['deviation'], 2).'%', 'tone' => $sCurve['deviation'] >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'],
                    ['label' => 'Laporan belum masuk', 'value' => number_format($sCurve['pendingReports']), 'tone' => 'bg-[#FFF5D6] text-[#A16207]'],
                ] as $summary)
                    <div class="rounded-xl border border-[#E3E8EF] bg-[#F8FAFC] p-4">
                        <p class="text-xs font-medium text-[#5B6B80]">{{ $summary['label'] }}</p>
                        <p class="mt-2 text-xl font-bold text-[#0A2342]">{{ $summary['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="relative mt-6 h-[300px] w-full sm:h-[360px]">
                @if (count($sCurve['labels']) > 0)
                    <canvas id="progress-s-curve" aria-label="Grafik kurva S progress proyek" role="img"></canvas>
                @else
                    <div class="flex h-full items-center justify-center rounded-xl border border-dashed border-[#D7E0EA] bg-[#F8FAFC] text-sm text-[#5B6B80]">Belum ada proyek dengan jadwal yang dapat dianalisis.</div>
                @endif
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-[#5B6B80]">
                <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#0A2342]"></span>Rencana kumulatif (baseline estimasi)</span>
                <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#14B8A6]"></span>Realisasi kumulatif</span>
                <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full border-2 border-dashed border-[#F59E0B]"></span>Minggu berjalan</span>
            </div>
        </section>

        <section class="rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            <div class="flex flex-col gap-3 border-b border-[#E3E8EF] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h3 class="font-bold text-[#0A2342]">Daftar laporan progress</h3>
                    <p class="mt-1 text-sm text-[#5B6B80]">Pantau laporan yang sudah masuk dari seluruh proyek.</p>
                </div>
                <a href="{{ route('laporan-dokumentasi.index') }}" class="text-sm font-semibold text-[#2A6F97] hover:text-[#0A2342]">Lihat dokumentasi</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-[#F8FAFC] text-xs uppercase tracking-wide text-[#5B6B80]">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Tanggal</th>
                            <th class="px-6 py-3 font-semibold">Proyek</th>
                            <th class="px-6 py-3 font-semibold">Progress</th>
                            <th class="px-6 py-3 font-semibold">Pelapor</th>
                            <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3E8EF]">
                        @forelse ($progressProyek as $progress)
                            <tr class="transition hover:bg-[#F8FAFC]">
                                <td class="whitespace-nowrap px-6 py-4 text-[#5B6B80]">{{ $progress->tanggal_laporan?->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-[#0F1B2D]">{{ $progress->proyek?->nama_proyek ?? '-' }}</p>
                                    <p class="mt-1 max-w-sm truncate text-xs text-[#5B6B80]">{{ $progress->uraian_pekerjaan }}</p>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 font-bold text-[#2A6F97]">{{ $progress->persentase_progress }}%</td>
                                <td class="whitespace-nowrap px-6 py-4 text-[#5B6B80]">{{ $progress->user?->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a href="{{ route('progress-proyek.show', $progress) }}" class="font-semibold text-[#2A6F97] hover:text-[#0A2342]">Detail</a>
                                    <a href="{{ route('progress-proyek.edit', $progress) }}" class="ml-3 font-semibold text-[#5B6B80] hover:text-[#0A2342]">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-[#5B6B80]">Belum ada data progress proyek.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @if (count($sCurve['labels']) > 0)
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const canvas = document.getElementById('progress-s-curve');
                if (!canvas || !window.Chart) return;
                const labels = @json($sCurve['labels']);
                const planned = @json($sCurve['planned']);
                const actual = @json($sCurve['actual']);
                const currentWeek = @json($sCurve['currentWeek']);

                new window.Chart(canvas, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Rencana kumulatif',
                                data: planned,
                                borderColor: '#0A2342',
                                backgroundColor: 'rgba(10, 35, 66, 0.08)',
                                borderWidth: 2,
                                pointRadius: 2,
                                pointHoverRadius: 5,
                                tension: 0.35,
                                fill: true,
                            },
                            {
                                label: 'Realisasi kumulatif',
                                data: actual,
                                borderColor: '#14B8A6',
                                backgroundColor: '#14B8A6',
                                borderWidth: 3,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                tension: 0.35,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => context.dataset.label + ': ' + Number(context.parsed.y).toFixed(2) + '%',
                                },
                            },
                        },
                        scales: {
                            y: {
                                min: 0,
                                max: 100,
                                ticks: { callback: (value) => value + '%' },
                                grid: { color: '#E3E8EF' },
                            },
                            x: { grid: { display: false } },
                        },
                    },
                    plugins: [{
                        id: 'currentWeekMarker',
                        afterDraw(chart) {
                            if (!currentWeek || !chart.chartArea) return;
                            const index = Math.min(currentWeek - 1, labels.length - 1);
                            const x = chart.scales.x.getPixelForValue(index);
                            const { top, bottom } = chart.chartArea;
                            const ctx = chart.ctx;
                            ctx.save();
                            ctx.beginPath();
                            ctx.setLineDash([5, 5]);
                            ctx.strokeStyle = '#F59E0B';
                            ctx.lineWidth = 2;
                            ctx.moveTo(x, top);
                            ctx.lineTo(x, bottom);
                            ctx.stroke();
                            ctx.restore();
                        },
                    }],
                });
            });
        </script>
    @endif
</x-app-layout>
