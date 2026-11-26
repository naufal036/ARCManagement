<?php

namespace App\Http\Controllers;

use App\Models\ProgressDokumentasi;
use App\Models\ProgressProyek;
use App\Models\Proyek;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgressProyekController extends Controller
{
    // Menampilkan daftar laporan progress proyek
    public function index(Request $request)
    {
        $progressProyek = ProgressProyek::with(['user', 'proyek'])
            ->latest('tanggal_laporan')
            ->get();

        $proyeks = Proyek::query()->orderBy('nama_proyek')->get();
        $selectedProject = $request->filled('project_id')
            ? $proyeks->firstWhere('id', (int) $request->integer('project_id'))
            : $proyeks->first();

        return view('progress-proyek.index', [
            'progressProyek' => $progressProyek,
            'proyeks' => $proyeks,
            'selectedProject' => $selectedProject,
            'sCurve' => $this->buildSCurve($selectedProject),
        ]);
    }

    private function buildSCurve(?Proyek $project): array
    {
        if ($project === null || $project->tanggal_mulai === null) {
            return [
                'labels' => [],
                'planned' => [],
                'actual' => [],
                'currentWeek' => 0,
                'plannedProgress' => 0,
                'actualProgress' => 0,
                'deviation' => 0,
                'pendingReports' => 0,
                'baselineLabel' => 'Baseline estimasi belum tersedia',
            ];
        }

        $start = Carbon::parse($project->tanggal_mulai)->startOfDay();
        $end = $project->tanggal_selesai
            ? Carbon::parse($project->tanggal_selesai)->startOfDay()
            : $start->copy()->addWeeks(12);

        if ($end->lessThan($start)) {
            $end = $start->copy();
        }

        $totalWeeks = max(1, (int) $start->diffInWeeks($end) + 1);
        $today = Carbon::today();
        $currentWeek = $today->lt($start)
            ? 0
            : min($totalWeeks, (int) $start->diffInWeeks($today->greaterThan($end) ? $end : $today) + 1);

        $reports = $project->progressProyek()
            ->whereBetween('tanggal_laporan', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal_laporan')
            ->get(['tanggal_laporan', 'persentase_progress']);

        $weeklyActual = array_fill(0, $totalWeeks, null);
        $reportedWeeks = [];

        foreach ($reports as $report) {
            $reportDate = Carbon::parse($report->tanggal_laporan);
            $week = min($totalWeeks - 1, (int) $start->diffInWeeks($reportDate));
            $weeklyActual[$week] = (float) $report->persentase_progress;

            if ($week < $currentWeek) {
                $reportedWeeks[$week] = true;
            }
        }

        $labels = [];
        $planned = [];
        $actual = [];
        $latestActual = 0.0;

        for ($week = 0; $week < $totalWeeks; $week++) {
            $labels[] = 'Minggu '.($week + 1);
            $planned[] = round(min(100, (($week + 1) / $totalWeeks) * 100), 2);

            if ($weeklyActual[$week] !== null) {
                $latestActual = $weeklyActual[$week];
            }

            $actual[] = round($latestActual, 2);
        }

        $currentIndex = max(0, $currentWeek - 1);
        $plannedProgress = $currentWeek > 0 ? $planned[$currentIndex] : 0;
        $actualProgress = $currentWeek > 0 ? $actual[$currentIndex] : 0;

        return [
            'labels' => $labels,
            'planned' => $planned,
            'actual' => $actual,
            'currentWeek' => $currentWeek,
            'plannedProgress' => $plannedProgress,
            'actualProgress' => $actualProgress,
            'deviation' => round($actualProgress - $plannedProgress, 2),
            'pendingReports' => max(0, $currentWeek - count($reportedWeeks)),
            'baselineLabel' => 'Baseline estimasi linear dari tanggal mulai sampai selesai; target mingguan belum tersedia.',
        ];
    }

    // Menampilkan form untuk membuat laporan progress proyek baru
    public function create()
    {
        return view('progress-proyek.create', [
            'proyeks' => Proyek::orderBy('nama_proyek')->get(),
        ]);
    }

    // Menyimpan laporan progress proyek baru ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'proyek_id' => 'required|exists:proyek,id',
            'tanggal_laporan' => 'required|date',
            'persentase_progress' => 'required|numeric|min:0|max:100',
            'uraian_pekerjaan' => 'required|string',
            'kendala' => 'nullable|string',
            'solusi' => 'nullable|string',
            'catatan' => 'nullable|string',
            'rencana_selanjutnya' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();

        $progress = ProgressProyek::create($validated);

        return redirect()
            ->route('progress-proyek.show', $progress->id)
            ->with('success', 'Laporan progress berhasil disimpan.');
    }

    // Menampilkan detail laporan progress proyek tertentu
    public function show(ProgressProyek $progressProyek)
    {
        $progress = $progressProyek->load([
            'user',
            'proyek',
            'dokumentasi' => fn ($query) => $query->latest(),
        ]);

        return view('progress-proyek.show', compact('progress'));
    }

    public function edit(ProgressProyek $progressProyek)
    {
        return view('progress-proyek.edit', [
            'progress' => $progressProyek->load(['user', 'proyek']),
        ]);
    }

    public function update(Request $request, ProgressProyek $progressProyek): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_laporan' => ['required', 'date'],
            'persentase_progress' => ['required', 'numeric', 'min:0', 'max:100'],
            'uraian_pekerjaan' => ['required', 'string'],
            'kendala' => ['nullable', 'string'],
            'solusi' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'rencana_selanjutnya' => ['nullable', 'string'],
        ]);
        $progressProyek->update($validated);

        return redirect()
            ->route('progress-proyek.show', $progressProyek)
            ->with('success', 'Laporan progress berhasil diperbarui.');
    }

    public function destroy(ProgressProyek $progressProyek): RedirectResponse
    {
        $disk = Storage::disk('local');
        $directory = 'progress-dokumentasi/'.$progressProyek->getKey().'/';
        $paths = $progressProyek->dokumentasi()
            ->pluck('path_file')
            ->filter(fn (string $path): bool => Str::startsWith($path, $directory))
            ->values()
            ->all();

        DB::transaction(function () use ($progressProyek): void {
            $progressProyek->delete();
        });

        if ($paths !== [] && ! $disk->delete($paths)) {
            Log::warning('Sebagian file dokumentasi gagal dihapus.', [
                'progress_proyek_id' => $progressProyek->getKey(),
                'paths' => $paths,
            ]);

            return redirect()
                ->route('progress-proyek.index')
                ->with('error', 'Laporan berhasil dihapus, tetapi sebagian file dokumentasi gagal dibersihkan.');
        }

        return redirect()
            ->route('progress-proyek.index')
            ->with('success', 'Laporan progress berhasil dihapus.');
    }

    public function storeDocumentation(Request $request, ProgressProyek $progressProyek)
    {
        Gate::authorize('uploadDocumentation', $progressProyek);

        $validated = $request->validate([
            'dokumentasi_file' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,pdf',
                'mimetypes:image/jpeg,image/png,application/pdf',
            ],
            'keterangan' => ['nullable', 'string', 'max:5000'],
        ]);

        $file = $validated['dokumentasi_file'];
        $extension = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            default => abort(422, 'Tipe file tidak didukung.'),
        };

        $directory = 'progress-dokumentasi/'.$progressProyek->getKey();
        $storedName = Str::uuid().'.'.$extension;
        $path = $file->storeAs($directory, $storedName, 'local');

        if ($path === false) {
            abort(500, 'File dokumentasi gagal disimpan.');
        }

        try {
            $progressProyek->dokumentasi()->create([
                'nama_file_asli' => Str::substr(basename($file->getClientOriginalName()), 0, 255),
                'path_file' => $path,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        return back()->with('success', 'Dokumentasi berhasil diunggah.');
    }

    public function viewDocumentation(
        ProgressProyek $progressProyek,
        ProgressDokumentasi $dokumentasi
    ) {
        $this->ensureDocumentationBelongsToProgress($progressProyek, $dokumentasi);
        Gate::authorize('viewDocumentation', $progressProyek);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($dokumentasi->path_file), 404);

        return response()->file($disk->path($dokumentasi->path_file));
    }

    public function downloadDocumentation(
        ProgressProyek $progressProyek,
        ProgressDokumentasi $dokumentasi
    ) {
        $this->ensureDocumentationBelongsToProgress($progressProyek, $dokumentasi);
        Gate::authorize('viewDocumentation', $progressProyek);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($dokumentasi->path_file), 404);

        $downloadName = preg_replace(
            '/[^A-Za-z0-9._ -]/',
            '_',
            basename($dokumentasi->nama_file_asli)
        ) ?: 'dokumentasi';

        return $disk->download($dokumentasi->path_file, $downloadName);
    }

    private function ensureDocumentationBelongsToProgress(
        ProgressProyek $progressProyek,
        ProgressDokumentasi $dokumentasi
    ): void {
        abort_unless(
            $dokumentasi->progress_proyek_id === $progressProyek->getKey()
                && Str::startsWith(
                    $dokumentasi->path_file,
                    'progress-dokumentasi/'.$progressProyek->getKey().'/'
                ),
            404
        );
    }
}
