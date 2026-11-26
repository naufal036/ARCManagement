<?php

namespace App\Http\Controllers;

use App\Enums\DokumenVerificationStatus;
use App\Models\ActivityLog;
use App\Models\DokumenProyek;
use App\Models\KategoriDokumen;
use App\Models\Klien;
use App\Models\ProgressProyek;
use App\Models\ProjectAssignment;
use App\Models\Proyek;
use App\Models\User;
use App\Models\VersiDokumen;
use App\Services\DokumenFileService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DokumenProyekController extends Controller
{
    public function __construct(private readonly DokumenFileService $fileService) {}

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
            'status' => ['nullable', Rule::in(DokumenVerificationStatus::values())],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'uploaded_from' => ['nullable', 'date'],
            'uploaded_to' => ['nullable', 'date', 'after_or_equal:uploaded_from'],
            'revised_from' => ['nullable', 'date'],
            'revised_to' => ['nullable', 'date', 'after_or_equal:revised_from'],
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
            ->with(['proyek.klien', 'kategoriDokumen', 'versiTerbaru.pengunggah'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query->where('nama_dokumen', 'like', '%'.$search.'%')->orWhere('nomor_dokumen', 'like', '%'.$search.'%')))
            ->when($filters['proyek_id'] ?? null, fn (Builder $query, int $projectId) => $query->where('proyek_id', $projectId))
            ->when($filters['client_id'] ?? null, fn (Builder $query, int $clientId) => $query->whereHas('proyek', fn (Builder $projectQuery) => $projectQuery->where('klien_id', $clientId)))
            ->when($filters['kategori_dokumen_id'] ?? null, fn (Builder $query, int $categoryId) => $query->where('kategori_dokumen_id', $categoryId))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status_verifikasi', $status))
            ->when($filters['user_id'] ?? null, fn (Builder $query, int $userId) => $query->whereHas('versiDokumen', fn (Builder $versionQuery) => $versionQuery->where('user_id', $userId)))
            ->when($filters['uploaded_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('dokumen_proyek.created_at', '>=', $date))
            ->when($filters['uploaded_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('dokumen_proyek.created_at', '<=', $date))
            ->when($filters['ekstensi'] ?? null, fn (Builder $query, string $extension) => $query->whereHas('versiDokumen', fn (Builder $versionQuery) => $versionQuery->where('ekstensi', strtolower($extension))))
            ->when($filters['revised_from'] ?? null, fn (Builder $query, string $date) => $query->whereHas('versiDokumen', fn (Builder $versionQuery) => $versionQuery->whereDate('created_at', '>=', $date)))
            ->when($filters['revised_to'] ?? null, fn (Builder $query, string $date) => $query->whereHas('versiDokumen', fn (Builder $versionQuery) => $versionQuery->whereDate('created_at', '<=', $date)))
            ->when(($filters['format'] ?? 'all') !== 'all' && isset($formatExtensions[$filters['format'] ?? '']), fn (Builder $query) => $query->whereHas('versiDokumen', fn (Builder $versionQuery) => $versionQuery->whereIn('ekstensi', $formatExtensions[$filters['format']])))
            ->latest();

        return view('dokumen-proyek.index', [
            'dokumenProyek' => $query->paginate(12)->withQueryString(),
            'proyeks' => $this->visibleProjects($user),
            'klien' => Klien::query()
                ->whereIn('id', $this->visibleProjectQuery($user)->select('klien_id'))
                ->orderBy('nama_klien')
                ->get(),
            'progressProyek' => $this->visibleProgress($user),
            'kategoriDokumen' => KategoriDokumen::query()->orderBy('nama_kategori')->get(),
            'filters' => $filters,
            'statuses' => DokumenVerificationStatus::cases(),
            'users' => User::query()
                ->whereIn('id', VersiDokumen::query()
                    ->whereIn('dokumen_proyek_id', (clone $visibleDocuments)->select('id'))
                    ->select('user_id'))
                ->orderBy('name')
                ->get(['id', 'name']),
            'dmsSummary' => [
                'documents' => (clone $visibleDocuments)->count(),
                'categories' => $this->visibleCategories($user),
                'versions' => VersiDokumen::query()
                    ->whereHas('dokumenProyek', fn (Builder $documentQuery) => $this->applyVisibility($documentQuery, $user))
                    ->count(),
            ],
            'recentDocuments' => (clone $visibleDocuments)
                ->with(['proyek.klien', 'versiTerbaru.pengunggah'])
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
            'kategoriDokumen' => KategoriDokumen::query()->where('status', 'active')->orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'proyek_id' => ['required', 'exists:proyek,id'],
            'kategori_dokumen_id' => [
                'required',
                Rule::exists('kategori_dokumen', 'id')->where('status', 'active'),
            ],
            'progress_proyek_id' => ['nullable', 'exists:progress_proyek,id'],
            'nama_dokumen' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ]);

        $project = Proyek::findOrFail($validated['proyek_id']);
        Gate::authorize('create', [DokumenProyek::class, $project]);
        $this->ensureProgressBelongsToProject($validated['progress_proyek_id'] ?? null, $project->getKey());

        $document = DB::transaction(function () use ($validated, $project): DokumenProyek {
            KategoriDokumen::query()
                ->whereKey($validated['kategori_dokumen_id'])
                ->where('status', 'active')
                ->lockForUpdate()
                ->firstOrFail();

            $document = DokumenProyek::create($validated);

            ActivityLog::record(
                'document_created',
                'Dokumen proyek dibuat.',
                $document,
                properties: [
                    'project_id' => $project->getKey(),
                    'category_id' => $document->kategori_dokumen_id,
                    'after' => $document->only(['nomor_dokumen', 'nama_dokumen', 'proyek_id', 'kategori_dokumen_id', 'status_verifikasi']),
                ]
            );

            return $document;
        });

        return redirect()->route('dokumen-proyek.show', $document)->with('success', 'Dokumen proyek berhasil dibuat.');
    }

    public function show(DokumenProyek $dokumenProyek): View
    {
        Gate::authorize('view', $dokumenProyek);
        $dokumenProyek->load([
            'proyek',
            'kategoriDokumen',
            'progressProyek',
            'versiTerbaru',
            'versiDokumen.pengunggah',
            'activityLogs.user',
        ]);

        return view('dokumen-proyek.show', [
            'dokumenProyek' => $dokumenProyek,
            'hasCurrentReviewPass' => ActivityLog::query()
                ->where('subject_type', class_basename(DokumenProyek::class))
                ->where('subject_id', $dokumenProyek->getKey())
                ->where('activity', 'document_review_pass')
                ->where('properties->verification_cycle', $dokumenProyek->verification_cycle)
                ->exists(),
        ]);
    }

    public function edit(DokumenProyek $dokumenProyek): View
    {
        Gate::authorize('update', $dokumenProyek);

        return view('dokumen-proyek.edit', [
            'dokumenProyek' => $dokumenProyek,
            'proyeks' => $this->visibleProjects(request()->user()),
            'progressProyek' => $this->visibleProgress(request()->user()),
            'kategoriDokumen' => KategoriDokumen::query()
                ->where(fn (Builder $query) => $query->where('status', 'active')->orWhereKey($dokumenProyek->kategori_dokumen_id))
                ->orderBy('nama_kategori')
                ->get(),
        ]);
    }

    public function update(Request $request, DokumenProyek $dokumenProyek): RedirectResponse
    {
        Gate::authorize('update', $dokumenProyek);

        $validated = $request->validate([
            'proyek_id' => ['required', 'exists:proyek,id'],
            'kategori_dokumen_id' => ['required', 'exists:kategori_dokumen,id'],
            'progress_proyek_id' => ['nullable', 'exists:progress_proyek,id'],
            'nama_dokumen' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'lock_version' => ['required', 'integer', 'min:0'],
        ]);

        $project = Proyek::findOrFail($validated['proyek_id']);
        Gate::authorize('create', [DokumenProyek::class, $project]);
        $this->ensureProgressBelongsToProject($validated['progress_proyek_id'] ?? null, $project->getKey());
        DB::transaction(function () use ($dokumenProyek, $validated, $project): void {
            $document = DokumenProyek::query()
                ->whereKey($dokumenProyek->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            Gate::authorize('update', $document);

            if ($document->lock_version !== (int) $validated['lock_version']) {
                abort(409, 'Dokumen telah diubah oleh pengguna lain. Muat ulang halaman sebelum menyimpan.');
            }

            $category = KategoriDokumen::query()
                ->whereKey($validated['kategori_dokumen_id'])
                ->lockForUpdate()
                ->firstOrFail();
            if ($category->status !== 'active' && $category->getKey() !== $document->kategori_dokumen_id) {
                throw ValidationException::withMessages([
                    'kategori_dokumen_id' => 'Kategori nonaktif tidak dapat dipilih untuk dokumen lain.',
                ]);
            }

            $before = $document->only(['nomor_dokumen', 'nama_dokumen', 'deskripsi', 'proyek_id', 'kategori_dokumen_id', 'progress_proyek_id', 'status_verifikasi']);
            $updates = collect($validated)->only([
                'proyek_id',
                'kategori_dokumen_id',
                'progress_proyek_id',
                'nama_dokumen',
                'deskripsi',
            ])->all();
            $document->fill($updates);
            $document->status_verifikasi = DokumenVerificationStatus::Draft;
            $document->lock_version++;
            $document->save();

            ActivityLog::record(
                'document_metadata_updated',
                'Metadata dokumen diperbarui; status dikembalikan ke Draft untuk pemeriksaan ulang.',
                $document,
                properties: [
                    'project_id' => $project->getKey(),
                    'category_id' => $document->kategori_dokumen_id,
                    'before' => $before,
                    'after' => $document->fresh()->only(['nomor_dokumen', 'nama_dokumen', 'deskripsi', 'proyek_id', 'kategori_dokumen_id', 'progress_proyek_id', 'status_verifikasi']),
                ]
            );
        });

        return redirect()->route('dokumen-proyek.show', $dokumenProyek)->with('success', 'Dokumen proyek berhasil diperbarui.');
    }

    public function destroy(DokumenProyek $dokumenProyek): RedirectResponse
    {
        Gate::authorize('delete', $dokumenProyek);

        $paths = $dokumenProyek->versiDokumen()->pluck('path_file')->all();

        DB::transaction(function () use ($dokumenProyek): void {
            ActivityLog::record(
                'document_deleted',
                'Dokumen proyek dan metadata versinya dihapus.',
                $dokumenProyek,
                properties: [
                    'project_id' => $dokumenProyek->proyek_id,
                    'category_id' => $dokumenProyek->kategori_dokumen_id,
                    'before' => $dokumenProyek->only(['nomor_dokumen', 'nama_dokumen', 'proyek_id', 'kategori_dokumen_id', 'status_verifikasi']),
                ]
            );
            $dokumenProyek->delete();
        });

        if ($paths !== [] && ! $this->fileService->cleanup('local', $paths)) {
            return redirect()->route('dokumen-proyek.index')
                ->with('error', 'Metadata terhapus, tetapi sebagian file gagal dibersihkan.');
        }

        return redirect()->route('dokumen-proyek.index')->with('success', 'Dokumen proyek berhasil dihapus.');
    }

    private function visibleProjects(User $user)
    {
        return $this->visibleProjectQuery($user)
            ->orderBy('nama_proyek')
            ->get();
    }

    private function visibleProjectQuery(User $user): Builder
    {
        $query = Proyek::query();

        if (! $this->canManageAll($user)) {
            $query->whereIn('id', ProjectAssignment::query()
                ->where('user_id', $user->getKey())
                ->select('project_id'));
        }

        return $query;
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
        $query = KategoriDokumen::query()->where('status', 'active');

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
