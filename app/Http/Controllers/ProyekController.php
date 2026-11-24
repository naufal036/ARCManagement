<?php

namespace App\Http\Controllers;

use App\Models\DokumenProyek;
use App\Models\JenisPekerjaan;
use App\Models\KategoriDokumen;
use App\Models\Klien;
use App\Models\Proyek;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProyekController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'string', 'max:30'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2200'],
        ]);

        $projects = Proyek::query()
            ->with(['klien', 'jenisPekerjaan'])
            ->withMax('progressProyek', 'persentase_progress')
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $searchQuery) use ($search): void {
                    $searchQuery->where('nama_proyek', 'like', '%'.$search.'%')
                        ->orWhere('kode_proyek', 'like', '%'.$search.'%')
                        ->orWhereHas('klien', fn (Builder $clientQuery) => $clientQuery->where('nama_klien', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['year'] ?? null, fn (Builder $query, int $year) => $query->whereYear('tanggal_mulai', $year))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('proyek.index', [
            'proyek' => $projects,
            'filters' => $filters,
            'statuses' => Proyek::query()->select('status')->distinct()->orderBy('status')->pluck('status'),
            'years' => Proyek::query()->selectRaw('YEAR(tanggal_mulai) as year')->distinct()->orderByDesc('year')->pluck('year'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('proyek.create', [
            'klien' => Klien::orderBy('nama_klien')->get(),
            'jenisPekerjaan' => JenisPekerjaan::query()->where('is_active', true)->orderBy('nama_pekerjaan')->get(),
        ]);
    }

    public function show(Proyek $proyek): View
    {
        return view('proyek.show', [
            'proyek' => $proyek->load([
                'klien',
                'jenisPekerjaan',
                'dokumenProyek.kategoriDokumen',
                'progressProyek' => fn ($query) => $query->latest('tanggal_laporan'),
            ]),
            'folderCategories' => collect(['BIMTEK', 'BKU', 'CCO', 'FOTO', 'GAMBAR', 'PROGRES', 'RAB', 'SURVEY', 'TERMIN'])
                ->map(function (string $name) use ($proyek): array {
                    $category = KategoriDokumen::query()->where('nama_kategori', $name)->first();
                    $fileCount = $proyek->dokumenProyek->filter(
                        fn (DokumenProyek $document): bool => strcasecmp($document->kategoriDokumen?->nama_kategori ?? '', $name) === 0
                    )->count();

                    return [
                        'name' => $name,
                        'count' => $fileCount,
                        'category_id' => $category?->getKey(),
                    ];
                }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_proyek' => ['required', 'string', 'max:30', 'unique:proyek,kode_proyek'],
            'nama_proyek' => ['required', 'string', 'max:150'],
            'klien_id' => ['required', 'exists:klien,id'],
            'jenis_pekerjaan_id' => ['required', 'exists:jenis_pekerjaan,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:30'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $cover = $request->file('cover_image');
        unset($validated['cover_image']);
        $project = Proyek::create($validated);

        if ($cover !== null) {
            $project->update(['cover_image_path' => $cover->store('project-covers', 'public')]);
        }

        return Redirect::route('proyek.index')->with('status', 'proyek-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proyek $proyek): View
    {
        return view('proyek.edit', [
            'proyek' => $proyek,
            'klien' => Klien::orderBy('nama_klien')->get(),
            'jenisPekerjaan' => JenisPekerjaan::query()
                ->where(fn (Builder $query) => $query->where('is_active', true)->orWhereKey($proyek->jenis_pekerjaan_id))
                ->orderBy('nama_pekerjaan')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proyek $proyek)
    {
        $validated = $request->validate([
            'kode_proyek' => ['required', 'string', 'max:30', 'unique:proyek,kode_proyek,'.$proyek->id],
            'nama_proyek' => ['required', 'string', 'max:150'],
            'klien_id' => ['required', 'exists:klien,id'],
            'jenis_pekerjaan_id' => ['required', 'exists:jenis_pekerjaan,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:30'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $cover = $request->file('cover_image');
        unset($validated['cover_image']);
        $proyek->update($validated);

        if ($cover !== null) {
            $oldCover = $proyek->cover_image_path;
            $proyek->update(['cover_image_path' => $cover->store('project-covers', 'public')]);

            if ($oldCover !== null) {
                Storage::disk('public')->delete($oldCover);
            }
        }

        return Redirect::route('proyek.index')->with('status', 'proyek-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proyek $proyek)
    {
        $cover = $proyek->cover_image_path;
        $proyek->delete();

        if ($cover !== null) {
            Storage::disk('public')->delete($cover);
        }

        return Redirect::route('proyek.index')->with('status', 'proyek-deleted');
    }

    public function removeCover(Proyek $proyek): RedirectResponse
    {
        $cover = $proyek->cover_image_path;
        $proyek->update(['cover_image_path' => null]);

        if ($cover !== null) {
            Storage::disk('public')->delete($cover);
        }

        return Redirect::route('proyek.edit', $proyek)->with('status', 'proyek-cover-deleted');
    }
}
