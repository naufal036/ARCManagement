<?php

namespace App\Http\Controllers;

use App\Models\DokumenProyek;
use App\Models\KategoriDokumen;
use App\Models\Klien;
use App\Models\ProgressProyek;
use App\Models\ProjectAssignment;
use App\Models\Proyek;
use App\Models\User;
use App\Models\VersiDokumen;
use App\Services\DokumenFileService;
use App\Services\DokumenVersionService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DokumenProyekController extends Controller
{
    public function __construct(
        private readonly DokumenFileService $fileService,
        private readonly DokumenVersionService $versionService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'proyek_id' => ['nullable', 'integer', 'exists:proyek,id'],
            'client_id' => ['nullable', 'integer', 'exists:klien,id'],
            'kategori_dokumen_id' => ['nullable', 'integer', 'exists:kategori_dokumen,id'],
            'ekstensi' => ['nullable', 'string', 'max:20'],
            'format' => ['nullable', 'in:all,pdf,excel,word,image,archive'],
        ]);

        $formatExtensions = [
            'pdf' => ['pdf'],
            'excel' => ['xls', 'xlsx'],
            'word' => ['doc', 'docx'],
            'image' => ['jpg', 'jpeg', 'png'],
            'archive' => ['zip', 'rar'],
        ];

        $visibleDocuments = $this->visibleDocuments($user);
        $query = (clone $visibleDocuments)
            ->with(['proyek', 'kategoriDokumen', 'versiTerbaru.pengunggah'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where('nama_dokumen', 'like', '%'.$search.'%'))
            ->when($filters['proyek_id'] ?? null, fn (Builder $query, int $projectId) => $query->where('proyek_id', $projectId))
            ->when($filters['client_id'] ?? null, fn (Builder $query, int $clientId) => $query->whereHas('proyek', fn (Builder $projectQuery) => $projectQuery->where('klien_id', $clientId)))
            ->when($filters['kategori_dokumen_id'] ?? null, fn (Builder $query, int $categoryId) => $query->where('kategori_dokumen_id', $categoryId))
            ->when($filters['ekstensi'] ?? null, fn (Builder $query, string $extension) => $query->whereHas('versiDokumen', fn (Builder $versionQuery) => $versionQuery->where('ekstensi', strtolower($extension))))
            ->when(($filters['format'] ?? 'all') !== 'all' && isset($formatExtensions[$filters['format'] ?? '']), fn (Builder $query) => $query->whereHas('versiDokumen', fn (Builder $versionQuery) => $versionQuery->whereIn('ekstensi', $formatExtensions[$filters['format']])))
            ->latest();

        return view('dokumen-proyek.index', [
            'dokumenProyek' => $query->paginate(12)->withQueryString(),
            'proyeks' => $this->visibleProjects($user),
            'klien' => Klien::query()->orderBy('nama_klien')->get(),
            'progressProyek' => $this->visibleProgress($user),
            'kategoriDokumen' => KategoriDokumen::query()->orderBy('nama_kategori')->get(),
            'filters' => $filters,
            'dmsSummary' => [
                'documents' => (clone $visibleDocuments)->count(),
                'categories' => $this->visibleCategories($user),
                'versions' => VersiDokumen::query()
                    ->whereHas('dokumenProyek', fn (Builder $documentQuery) => $this->applyVisibility($documentQuery, $user))
                    ->count(),
            ],
            'recentDocuments' => (clone $visibleDocuments)
                ->with(['proyek', 'versiTerbaru.pengunggah'])
                ->latest()
                ->limit(5)
                ->get(),
            'recentActivities' => VersiDokumen::query()
                ->whereHas('dokumenProyek', fn (Builder $documentQuery) => $this->applyVisibility($documentQuery, $user))
                ->with(['dokumenProyek.proyek', 'pengunggah'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('dokumen-proyek.create', [
            'proyeks' => $this->visibleProjects(request()->user()),
            'progressProyek' => $this->visibleProgress(request()->user()),
            'kategoriDokumen' => KategoriDokumen::query()->orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_dokumen' => ['required', 'string', 'max:100', 'unique:dokumen_proyek,nomor_dokumen'],
            'proyek_id' => ['required', 'exists:proyek,id'],
            'nama_dokumen' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'drive_url' => ['nullable', 'url', 'max:2048'],
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['required', 'file', 'max:51200'],
        ]);

        $project = Proyek::findOrFail($validated['proyek_id']);
        Gate::authorize('create', [DokumenProyek::class, $project]);
        $files = $request->file('files', []);
        unset($validated['files']);

        $document = DB::transaction(function () use ($validated, $files, $request): DokumenProyek {
            $document = DokumenProyek::create($validated);
            $this->versionService->upload($document, $files, $request->user());

            return $document;
        });

        return redirect()->route('dokumen-proyek.index')->with('success', 'Dokumen dan file berhasil disimpan.');
    }

    public function show(DokumenProyek $dokumenProyek): View
    {
        Gate::authorize('view', $dokumenProyek);

        return view('dokumen-proyek.show', [
            'dokumenProyek' => $dokumenProyek->load([
                'proyek',
                'kategoriDokumen',
                'progressProyek',
                'versiDokumen.pengunggah',
            ]),
        ]);
    }

    public function edit(DokumenProyek $dokumenProyek): View
    {
        Gate::authorize('update', $dokumenProyek);

        return view('dokumen-proyek.edit', [
            'dokumenProyek' => $dokumenProyek,
            'proyeks' => $this->visibleProjects(request()->user()),
            'progressProyek' => $this->visibleProgress(request()->user()),
            'kategoriDokumen' => KategoriDokumen::query()->orderBy('nama_kategori')->get(),
        ]);
    }

    public function update(Request $request, DokumenProyek $dokumenProyek): RedirectResponse
    {
        Gate::authorize('update', $dokumenProyek);

        $validated = $request->validate([
            'proyek_id' => ['required', 'exists:proyek,id'],
            'kategori_dokumen_id' => ['nullable', 'exists:kategori_dokumen,id'],
            'progress_proyek_id' => ['nullable', 'exists:progress_proyek,id'],
            'nama_dokumen' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ]);

        $project = Proyek::findOrFail($validated['proyek_id']);
        Gate::authorize('create', [DokumenProyek::class, $project]);
        $this->ensureProgressBelongsToProject($validated['progress_proyek_id'] ?? null, $project->getKey());
        $dokumenProyek->update($validated);

        return redirect()->route('dokumen-proyek.show', $dokumenProyek)->with('success', 'Dokumen proyek berhasil diperbarui.');
    }

    public function destroy(DokumenProyek $dokumenProyek): RedirectResponse
    {
        Gate::authorize('delete', $dokumenProyek);

        $paths = $dokumenProyek->versiDokumen()->pluck('path_file')->all();

        DB::transaction(fn () => $dokumenProyek->delete());

        if ($paths !== [] && ! $this->fileService->cleanup('local', $paths)) {
            return redirect()->route('dokumen-proyek.index')
                ->with('error', 'Metadata terhapus, tetapi sebagian file gagal dibersihkan.');
        }

        return redirect()->route('dokumen-proyek.index')->with('success', 'Dokumen proyek berhasil dihapus.');
    }

    private function visibleProjects(User $user)
    {
        $query = Proyek::query()->orderBy('nama_proyek');

        if (! $this->canManageAll($user)) {
            $query->whereIn('id', ProjectAssignment::query()
                ->where('user_id', $user->getKey())
                ->select('project_id'));
        }

        return $query->get();
    }

    private function visibleDocuments(User $user): Builder
    {
        return $this->applyVisibility(DokumenProyek::query(), $user);
    }

    private function visibleProgress(User $user)
    {
        return ProgressProyek::query()
            ->with('proyek')
            ->whereIn('proyek_id', $this->visibleProjects($user)->pluck('id'))
            ->latest('tanggal_laporan')
            ->get();
    }

    private function applyVisibility(Builder $query, User $user): Builder
    {
        if (! $this->canManageAll($user)) {
            $query->whereIn('proyek_id', ProjectAssignment::query()
                ->where('user_id', $user->getKey())
                ->select('project_id'));
        }

        return $query;
    }

    private function visibleCategories(User $user): int
    {
        $query = KategoriDokumen::query();

        if (! $this->canManageAll($user)) {
            $query->whereHas('dokumenProyek', fn (Builder $documentQuery) => $this->applyVisibility($documentQuery, $user));
        }

        return $query->count();
    }

    private function canManageAll(User $user): bool
    {
        return in_array($user->role?->nama_role, ['Admin', 'Direktur'], true);
    }

    private function ensureProgressBelongsToProject(?int $progressId, int $projectId): void
    {
        if ($progressId !== null && ! ProgressProyek::query()
            ->whereKey($progressId)
            ->where('proyek_id', $projectId)
            ->exists()) {
            abort(422, 'Laporan progress tidak terkait dengan proyek yang dipilih.');
        }
    }
}
