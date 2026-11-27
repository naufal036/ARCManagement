<?php

namespace App\Http\Controllers;

use App\Models\DokumenProyek;
use App\Models\JenisPekerjaan;
use App\Models\KategoriDokumen;
use App\Models\Klien;
use App\Models\Proyek;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProyekController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $query = Proyek::query()
            ->with(['klien', 'jenisPekerjaan'])
            ->withMax('progressProyek', 'persentase_progress');

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('nama_proyek', 'like', '%'.$search.'%')
                    ->orWhere('kode_proyek', 'like', '%'.$search.'%')
                    ->orWhereHas('klien', fn (Builder $clientQuery) => $clientQuery->where('nama_klien', 'like', '%'.$search.'%'));
            });
        }

        if ($request->filled('klien_id')) {
            $query->where('klien_id', $request->input('klien_id'));
        }

        if ($request->filled('jenis_pekerjaan_id')) {
            $query->where('jenis_pekerjaan_id', $request->input('jenis_pekerjaan_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal_mulai', (int) $request->input('year'));
        }

        $projectStatistics = Proyek::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COUNT(CASE WHEN project_type = ? THEN 1 END) as perencanaan', ['Perencanaan'])
            ->selectRaw('COUNT(CASE WHEN project_type = ? THEN 1 END) as pengawasan', ['Pengawasan'])
            ->first();

        return view('proyek.index', [
            'proyek' => $query->latest()->paginate(10)->withQueryString(),
            'totalProyek' => (int) ($projectStatistics->total ?? 0),
            'proyekPerencanaan' => (int) ($projectStatistics->perencanaan ?? 0),
            'proyekPengawasan' => (int) ($projectStatistics->pengawasan ?? 0),
            'klien' => Klien::orderBy('nama_klien')->get(['id', 'nama_klien']),
            'jenisPekerjaan' => JenisPekerjaan::orderBy('nama_pekerjaan')->get(['id', 'nama_pekerjaan']),
            'statuses' => Proyek::query()->select('status')->distinct()->orderBy('status')->pluck('status'),
            'years' => Proyek::query()->whereNotNull('tanggal_mulai')->pluck('tanggal_mulai')->map(fn ($d) => (int) Carbon::parse($d)->format('Y'))->unique()->values(),
            'filters' => [
                'search' => $search,
                'klien_id' => $request->input('klien_id'),
                'jenis_pekerjaan_id' => $request->input('jenis_pekerjaan_id'),
                'status' => $request->input('status'),
                'year' => $request->input('year'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('proyek.create', [
            'klien' => Klien::orderBy('nama_klien')->get(),
            'jenisPekerjaan' => JenisPekerjaan::query()->where('is_active', true)->orderBy('nama_pekerjaan')->get(),
            'selectedKlienId' => $request->input('klien_id'),
            'selectedJenisPekerjaanId' => $request->input('jenis_pekerjaan_id'),
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
                'contracts' => fn ($query) => $query->latest('contract_date'),
                'projectAssignments.user',
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
            'kode_proyek' => ['nullable', 'string', 'max:30', 'unique:proyek,kode_proyek'],
            'nama_proyek' => ['required', 'string', 'max:150'],
            'klien_id' => ['required', 'exists:klien,id'],
            'jenis_pekerjaan_id' => ['required', 'exists:jenis_pekerjaan,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'anggaran' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:30'],
            'nama_kegiatan' => ['nullable', 'string', 'max:255'], 'instansi' => ['nullable', 'string', 'max:255'], 'lokasi' => ['nullable', 'string', 'max:255'], 'waktu_pengerjaan' => ['nullable', 'integer', 'min:1'], 'nama_kontrak' => ['nullable', 'string', 'max:255'], 'nilai_kontrak' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'], 'project_type' => ['nullable', 'in:Perencanaan,Pengawasan'], 'drive_url' => ['nullable', 'url', 'max:2048'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $validated['project_type'] ??= 'Pengawasan';
        if (empty($validated['kode_proyek'])) {
            $validated['kode_proyek'] = 'PRJ-'.Str::ulid();
        }

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
            'anggaran' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:30'],
            'nama_kegiatan' => ['nullable', 'string', 'max:255'], 'instansi' => ['nullable', 'string', 'max:255'], 'lokasi' => ['nullable', 'string', 'max:255'], 'waktu_pengerjaan' => ['nullable', 'integer', 'min:1'], 'nama_kontrak' => ['nullable', 'string', 'max:255'], 'nilai_kontrak' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'], 'project_type' => ['nullable', 'in:Perencanaan,Pengawasan'], 'drive_url' => ['nullable', 'url', 'max:2048'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $cover = $request->file('cover_image');
        unset($validated['cover_image']);
        $validated['project_type'] ??= $proyek->project_type ?: 'Pengawasan';
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
        if (
            $proyek->contracts()->exists()
            || $proyek->projectAssignments()->exists()
            || $proyek->dokumenProyek()->exists()
            || $proyek->progressProyek()->exists()
        ) {
            return Redirect::route('proyek.index')
                ->withErrors(['proyek' => 'Proyek tidak dapat dihapus karena masih memiliki kontrak, penugasan, dokumen, atau laporan progress.']);
        }

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
