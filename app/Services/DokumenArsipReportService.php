<?php

namespace App\Services;

namespace App\Services;

use App\Enums\DokumenVerificationStatus;
use App\Models\ActivityLog;
use App\Models\DokumenProyek;
use App\Models\ProjectAssignment;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DokumenArsipReportService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<DokumenProyek>
     */
    public function documents(User $user, array $filters): Builder
    {
        $query = DokumenProyek::query()
            ->whereIn('proyek_id', $this->visibleProjectIds($user))
            ->with(['proyek.klien', 'kategoriDokumen', 'versiTerbaru.pengunggah'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('nama_dokumen', 'like', '%'.$search.'%')
                        ->orWhere('nomor_dokumen', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['project_id'] ?? null, fn (Builder $query, int $projectId) => $query->where('proyek_id', $projectId))
            ->when($filters['category_id'] ?? null, fn (Builder $query, int $categoryId) => $query->where('kategori_dokumen_id', $categoryId))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status_verifikasi', $status))
            ->when($filters['user_id'] ?? null, function (Builder $query, int $userId): void {
                $query->whereHas('versiDokumen', fn (Builder $versionQuery) => $versionQuery->where('user_id', $userId));
            });

        $period = $filters['period'] ?? 'all';
        $hasDates = ($filters['date_from'] ?? null) !== null || ($filters['date_to'] ?? null) !== null;

        if ($hasDates && $period === 'uploaded') {
            $query->where(function (Builder $query) use ($filters): void {
                $this->applyDateRange($query, $filters, 'created_at');
                $query->orWhereHas('versiDokumen', function (Builder $versionQuery) use ($filters): void {
                    $this->applyDateRange($versionQuery, $filters, 'created_at');
                });
            });
        } elseif ($period === 'uploaded') {
            $query->where(function (Builder $query): void {
                $query->whereHas('versiDokumen')
                    ->orWhereHas('activityLogs', fn (Builder $activityQuery) => $activityQuery->where('activity', 'document_created'));
            });
        } elseif ($hasDates && $period === 'revised') {
            $query->whereHas('versiDokumen', function (Builder $versionQuery) use ($filters): void {
                $versionQuery->where('nomor_versi', '>', 1);
                $this->applyDateRange($versionQuery, $filters, 'created_at');
            });
        } elseif ($period === 'revised') {
            $query->whereHas('versiDokumen', fn (Builder $versionQuery) => $versionQuery->where('nomor_versi', '>', 1));
        } elseif (in_array($period, ['approved', 'rejected'], true)) {
            $query->whereIn('id', $this->activityQuery($user, $filters, $period)->select('subject_id'));
        } elseif ($hasDates) {
            $query->where(function (Builder $query) use ($filters, $user): void {
                $this->applyDateRange($query, $filters, 'created_at');
                $query->orWhereHas('versiDokumen', function (Builder $versionQuery) use ($filters): void {
                    $this->applyDateRange($versionQuery, $filters, 'created_at');
                });

                $query->orWhereIn('id', $this->activityQuery($user, $filters)->select('subject_id'));
            });
        }

        return $query->orderByDesc('created_at')->orderByDesc('id');
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<ActivityLog>
     */
    public function activities(User $user, array $filters): Builder
    {
        return $this->activityQuery($user, $filters, $filters['period'] ?? 'all')
            ->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{
     *     total: int,
     *     statuses: array<string, int>,
     *     categories: Collection<int, object>,
     *     projects: Collection<int, object>
     * }
     */
    public function summary(User $user, array $filters): array
    {
        $documents = $this->documents($user, $filters);
        $statuses = [];

        foreach (DokumenVerificationStatus::cases() as $status) {
            $statuses[$status->value] = (clone $documents)
                ->where('status_verifikasi', $status->value)
                ->count();
        }

        $documentIds = (clone $documents)->select('dokumen_proyek.id');
        $categories = DB::table('dokumen_proyek')
            ->join('kategori_dokumen', 'kategori_dokumen.id', '=', 'dokumen_proyek.kategori_dokumen_id')
            ->whereIn('dokumen_proyek.id', $documentIds)
            ->select('kategori_dokumen.id', 'kategori_dokumen.kode_kategori', 'kategori_dokumen.nama_kategori')
            ->selectRaw('COUNT(dokumen_proyek.id) AS documents_count')
            ->groupBy('kategori_dokumen.id', 'kategori_dokumen.kode_kategori', 'kategori_dokumen.nama_kategori')
            ->orderBy('kategori_dokumen.nama_kategori')
            ->get();

        $projects = DB::table('dokumen_proyek')
            ->join('proyek', 'proyek.id', '=', 'dokumen_proyek.proyek_id')
            ->whereIn('dokumen_proyek.id', $documentIds)
            ->select('proyek.id', 'proyek.kode_proyek', 'proyek.nama_proyek')
            ->selectRaw('COUNT(dokumen_proyek.id) AS documents_count')
            ->groupBy('proyek.id', 'proyek.kode_proyek', 'proyek.nama_proyek')
            ->orderBy('proyek.nama_proyek')
            ->get();

        return [
            'total' => (clone $documents)->count(),
            'statuses' => $statuses,
            'categories' => $categories,
            'projects' => $projects,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<User>
     */
    public function uploaders(User $user): Builder
    {
        $visibleDocumentIds = DokumenProyek::query()
            ->whereIn('proyek_id', $this->visibleProjectIds($user))
            ->select('id');

        return User::query()
            ->whereIn('id', DB::table('versi_dokumen')
                ->whereIn('dokumen_proyek_id', $visibleDocumentIds)
                ->select('user_id'))
            ->orderBy('name');
    }

    /**
     * @return Builder<Proyek>
     */
    public function projects(User $user): Builder
    {
        return Proyek::query()
            ->whereIn('id', $this->visibleProjectIds($user))
            ->orderBy('nama_proyek');
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<ActivityLog>
     */
    private function activityQuery(User $user, array $filters, string $period = 'all'): Builder
    {
        $visibleProjectIds = $this->visibleProjectIds($user);
        $visibleDocumentIds = DokumenProyek::query()
            ->whereIn('proyek_id', $visibleProjectIds)
            ->select('id');

        $query = ActivityLog::query()
            ->where('subject_type', class_basename(DokumenProyek::class))
            ->where(function (Builder $query) use ($visibleProjectIds, $visibleDocumentIds): void {
                $query->whereIn('subject_id', $visibleDocumentIds)
                    ->orWhereIn('properties->project_id', $visibleProjectIds);
            })
            ->when($filters['project_id'] ?? null, function (Builder $query, int $projectId): void {
                $projectDocumentIds = DokumenProyek::query()
                    ->where('proyek_id', $projectId)
                    ->select('id');

                $query->where(function (Builder $query) use ($projectId, $projectDocumentIds): void {
                    $query->whereIn('subject_id', $projectDocumentIds)
                        ->orWhereJsonContains('properties->project_id', $projectId);
                });
            })
            ->when($filters['category_id'] ?? null, function (Builder $query, int $categoryId): void {
                $query->where(function (Builder $query) use ($categoryId): void {
                    $query->whereJsonContains('properties->category_id', $categoryId)
                        ->orWhereJsonContains('properties->before->kategori_dokumen_id', $categoryId)
                        ->orWhereJsonContains('properties->after->kategori_dokumen_id', $categoryId);
                });
            })
            ->when($filters['user_id'] ?? null, fn (Builder $query, int $userId) => $query->where('user_id', $userId));

        if (($filters['status'] ?? null) !== null) {
            $status = $filters['status'];
            $query->where(function (Builder $query) use ($status): void {
                $query->whereJsonContains('properties->before->status_verifikasi', $status)
                    ->orWhereJsonContains('properties->after->status_verifikasi', $status)
                    ->orWhereIn('subject_id', DokumenProyek::query()
                        ->where('status_verifikasi', $status)
                        ->select('id'));
            });
        }

        $this->applyDateRange($query, $filters, 'created_at');

        $periodActivities = match ($period) {
            'uploaded' => ['document_created', 'document_version_uploaded'],
            'revised' => ['document_version_uploaded'],
            'approved' => ['document_final_approved'],
            'rejected' => ['document_final_rejected', 'document_review_rejected'],
            default => null,
        };

        if ($periodActivities !== null) {
            $query->whereIn('activity', $periodActivities);
        }

        if ($period === 'revised') {
            $query->where('properties->version_number', '>', 1);
        }

        return $query;
    }

    private function visibleProjectIds(User $user): QueryBuilder
    {
        $query = Proyek::query()->select('id');

        if (! in_array($user->role?->name, ['Admin', 'Direktur'], true)) {
            $query->whereIn('id', ProjectAssignment::query()
                ->where('user_id', $user->getKey())
                ->select('project_id'));
        }

        return $query->toBase();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyDateRange(Builder $query, array $filters, string $column): void
    {
        $query->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate($column, '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate($column, '<=', $date));
    }
}
