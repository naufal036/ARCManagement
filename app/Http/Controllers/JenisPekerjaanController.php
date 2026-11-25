<?php

namespace App\Http\Controllers;

use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JenisPekerjaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = JenisPekerjaan::query()->withCount('proyek');
        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('nama_pekerjaan', 'like', '%'.$search.'%')
                    ->orWhere('deskripsi', 'like', '%'.$search.'%');
            });
        }

        if ($request->input('project_filter') === 'with_projects') {
            $query->has('proyek');
        } elseif ($request->input('project_filter') === 'without_projects') {
            $query->doesntHave('proyek');
        }

        return view('jenis-pekerjaan.index', [
            'jenisPekerjaan' => $query->orderBy('nama_pekerjaan')->paginate(10)->withQueryString(),
            'totalJenisPekerjaan' => JenisPekerjaan::count(),
            'jenisDenganProyek' => JenisPekerjaan::has('proyek')->count(),
            'search' => $search,
            'projectFilter' => $request->input('project_filter', 'all'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('jenis-pekerjaan.create');
    }

    public function show(JenisPekerjaan $jenisPekerjaan): View
    {
        return view('jenis-pekerjaan.show', [
            'jenisPekerjaan' => $jenisPekerjaan,
            'proyek' => $jenisPekerjaan->proyek()
                ->with('klien')
                ->latest()
                ->paginate(8),
            'jumlahProyek' => $jenisPekerjaan->proyek()->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge(['nama_pekerjaan' => trim((string) $request->input('nama_pekerjaan'))]);

        $validated = $request->validate([
            'nama_pekerjaan' => ['required', 'string', 'max:100', Rule::unique('jenis_pekerjaan', 'nama_pekerjaan')],
            'deskripsi' => ['nullable', 'string'],
        ]);

        JenisPekerjaan::create($validated);

        return Redirect::route('jenis-pekerjaan.index')->with('status', 'jenis-pekerjaan-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisPekerjaan $jenisPekerjaan): View
    {
        return view('jenis-pekerjaan.edit', [
            'jenisPekerjaan' => $jenisPekerjaan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisPekerjaan $jenisPekerjaan)
    {
        $request->merge(['nama_pekerjaan' => trim((string) $request->input('nama_pekerjaan'))]);

        $validated = $request->validate([
            'nama_pekerjaan' => ['required', 'string', 'max:100', Rule::unique('jenis_pekerjaan', 'nama_pekerjaan')->ignore($jenisPekerjaan->getKey())],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $jenisPekerjaan->update($validated);

        return Redirect::route('jenis-pekerjaan.index')->with('status', 'jenis-pekerjaan-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisPekerjaan $jenisPekerjaan)
    {
        if ($jenisPekerjaan->proyek()->exists()) {
            return Redirect::route('jenis-pekerjaan.index')
                ->withErrors(['jenis_pekerjaan' => 'Jenis pekerjaan tidak dapat dihapus karena masih digunakan oleh proyek.']);
        }

        $jenisPekerjaan->delete();

        return Redirect::route('jenis-pekerjaan.index')->with('status', 'jenis-pekerjaan-deleted');
    }
}
