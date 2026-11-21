<?php

namespace App\Http\Controllers;

use App\Models\JenisPekerjaan;
use App\Models\Klien;
use App\Models\Proyek;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
        $query = Proyek::query()->with(['klien', 'jenisPekerjaan']);

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('nama_proyek', 'like', '%'.$search.'%')
                    ->orWhere('kode_proyek', 'like', '%'.$search.'%');
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

        return view('proyek.index', [
            'proyek' => $query->latest()->paginate(10)->withQueryString(),
            'totalProyek' => Proyek::count(),
            'jumlahProyekPerStatus' => Proyek::query()
                ->select('status')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('status')
                ->orderBy('status')
                ->get(),
            'klien' => Klien::orderBy('nama_klien')->get(['id', 'nama_klien']),
            'jenisPekerjaan' => JenisPekerjaan::orderBy('nama_pekerjaan')->get(['id', 'nama_pekerjaan']),
            'statuses' => Proyek::query()->select('status')->distinct()->orderBy('status')->pluck('status'),
            'filters' => $request->only(['search', 'klien_id', 'jenis_pekerjaan_id', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('proyek.create', [
            'klien' => Klien::orderBy('nama_klien')->get(),
            'jenisPekerjaan' => JenisPekerjaan::orderBy('nama_pekerjaan')->get(),
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
                'progressProyek' => fn ($query) => $query->latest('tanggal_laporan'),
                'contracts' => fn ($query) => $query->latest('contract_date'),
                'projectAssignments.user',
                'dokumenProyek.kategoriDokumen',
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_proyek' => ['required', 'string', 'max:150'],
            'klien_id' => ['required', 'exists:klien,id'],
            'jenis_pekerjaan_id' => ['required', 'exists:jenis_pekerjaan,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'anggaran' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:30'],
        ]);

        $validated['kode_proyek'] = 'PRJ-'.Str::ulid();

        Proyek::create($validated);

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
            'jenisPekerjaan' => JenisPekerjaan::orderBy('nama_pekerjaan')->get(),
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
        ]);

        $proyek->update($validated);

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

        $proyek->delete();

        return Redirect::route('proyek.index')->with('status', 'proyek-deleted');
    }
}
