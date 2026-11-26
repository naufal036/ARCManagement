<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DokumenProyek;
use App\Models\KategoriDokumen;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KategoriDokumenController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', KategoriDokumen::class);

        return view('kategori-dokumen.index', [
            'kategoriDokumen' => KategoriDokumen::query()
                ->with('parent')
                ->withCount([
                    'dokumenProyek',
                    'children',
                    'dokumenProyek as approved_documents_count' => fn (Builder $query) => $query->where('status_verifikasi', 'approved'),
                    'dokumenProyek as pending_documents_count' => fn (Builder $query) => $query->where('status_verifikasi', 'pending_review'),
                    'dokumenProyek as rejected_documents_count' => fn (Builder $query) => $query->whereIn('status_verifikasi', ['rejected', 'revision_required']),
                ])
                ->orderBy('nama_kategori')
                ->paginate(12),
            'projectBreakdown' => DokumenProyek::query()
                ->join('proyek', 'proyek.id', '=', 'dokumen_proyek.proyek_id')
                ->select('dokumen_proyek.kategori_dokumen_id', 'proyek.nama_proyek')
                ->selectRaw('COUNT(dokumen_proyek.id) AS documents_count')
                ->groupBy('dokumen_proyek.kategori_dokumen_id', 'proyek.nama_proyek')
                ->get()
                ->groupBy('kategori_dokumen_id'),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', KategoriDokumen::class);

        return view('kategori-dokumen.create', [
            'parents' => $this->editableParents(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', KategoriDokumen::class);

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori_dokumen,nama_kategori'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'exists:kategori_dokumen,id'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ]);

        $category = DB::transaction(function () use ($validated): KategoriDokumen {
            $category = KategoriDokumen::create($validated);
            ActivityLog::record(
                'category_created',
                'Kategori dokumen dibuat.',
                $category,
                properties: ['after' => $category->only(['kode_kategori', 'parent_id', 'nama_kategori', 'deskripsi', 'status'])]
            );

            return $category;
        });

        return redirect()->route('kategori-dokumen.index')->with('success', 'Kategori dokumen berhasil dibuat.');
    }

    public function edit(KategoriDokumen $kategoriDokumen): View
    {
        Gate::authorize('update', $kategoriDokumen);

        return view('kategori-dokumen.edit', [
            'kategoriDokumen' => $kategoriDokumen,
            'parents' => $this->editableParents($kategoriDokumen),
        ]);
    }

    public function update(Request $request, KategoriDokumen $kategoriDokumen): RedirectResponse
    {
        Gate::authorize('update', $kategoriDokumen);

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', Rule::unique('kategori_dokumen', 'nama_kategori')->ignore($kategoriDokumen->getKey())],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:kategori_dokumen,id'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ]);

        $parentId = isset($validated['parent_id']) ? (int) $validated['parent_id'] : null;
        $this->ensureParentIsNotDescendant($kategoriDokumen, $parentId);

        DB::transaction(function () use ($kategoriDokumen, $validated): void {
            $before = $kategoriDokumen->only(['kode_kategori', 'parent_id', 'nama_kategori', 'deskripsi', 'status']);
            $kategoriDokumen->update($validated);

            ActivityLog::record(
                'category_updated',
                'Metadata kategori dokumen diperbarui.',
                $kategoriDokumen,
                properties: ['before' => $before, 'after' => $kategoriDokumen->fresh()->only(['kode_kategori', 'parent_id', 'nama_kategori', 'deskripsi', 'status'])]
            );
        });

        return redirect()->route('kategori-dokumen.index')->with('success', 'Kategori dokumen berhasil diperbarui.');
    }

    public function destroy(KategoriDokumen $kategoriDokumen): RedirectResponse
    {
        Gate::authorize('delete', $kategoriDokumen);

        if ($kategoriDokumen->dokumenProyek()->exists() || $kategoriDokumen->children()->exists()) {
            return redirect()->route('kategori-dokumen.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan atau memiliki subkategori.');
        }

        DB::transaction(function () use ($kategoriDokumen): void {
            ActivityLog::record(
                'category_deleted',
                'Kategori dokumen dihapus.',
                $kategoriDokumen,
                properties: ['before' => $kategoriDokumen->only(['kode_kategori', 'parent_id', 'nama_kategori', 'deskripsi', 'status'])]
            );
            $kategoriDokumen->delete();
        });

        return redirect()->route('kategori-dokumen.index')->with('success', 'Kategori dokumen berhasil dihapus.');
    }

    /**
     * @return Collection<int, KategoriDokumen>
     */
    private function editableParents(?KategoriDokumen $category = null): Collection
    {
        $excludedIds = $category === null
            ? []
            : [$category->getKey(), ...$this->descendantIds($category)];

        return KategoriDokumen::query()
            ->whereNotIn('id', $excludedIds)
            ->orderBy('nama_kategori')
            ->get();
    }

    private function ensureParentIsNotDescendant(KategoriDokumen $category, ?int $parentId): void
    {
        if ($parentId !== null && (
            $parentId === $category->getKey()
            || in_array($parentId, $this->descendantIds($category), true)
        )) {
            throw ValidationException::withMessages([
                'parent_id' => 'Kategori induk tidak boleh merupakan subkategori dari kategori ini.',
            ]);
        }
    }

    /**
     * @return array<int>
     */
    private function descendantIds(KategoriDokumen $category): array
    {
        $childrenByParent = KategoriDokumen::query()
            ->whereNotNull('parent_id')
            ->get(['id', 'parent_id'])
            ->groupBy('parent_id');
        $descendantIds = [];
        $pendingIds = [$category->getKey()];

        while ($pendingIds !== []) {
            $nextIds = [];

            foreach ($pendingIds as $parentId) {
                foreach ($childrenByParent->get($parentId, collect()) as $child) {
                    if (! in_array($child->id, $descendantIds, true)) {
                        $descendantIds[] = $child->id;
                        $nextIds[] = $child->id;
                    }
                }
            }

            $pendingIds = $nextIds;
        }

        return $descendantIds;
    }
}
