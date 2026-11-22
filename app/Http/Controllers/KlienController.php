<?php

namespace App\Http\Controllers;

use App\Models\Klien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KlienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Klien::query()->withCount('proyek');
        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $query->where('nama_klien', 'like', '%'.$search.'%');
        }

        if ($request->input('project_filter') === 'with_projects') {
            $query->has('proyek');
        } elseif ($request->input('project_filter') === 'without_projects') {
            $query->doesntHave('proyek');
        }

        return view('klien.index', [
            'klien' => $query->orderBy('nama_klien')->paginate(10)->withQueryString(),
            'totalKlien' => Klien::count(),
            'klienDenganProyek' => Klien::has('proyek')->count(),
            'search' => $search,
            'projectFilter' => $request->input('project_filter', 'all'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('klien.create');
    }

    public function show(Klien $klien): View
    {
        return view('klien.show', [
            'klien' => $klien,
            'proyek' => $klien->proyek()
                ->with('jenisPekerjaan')
                ->latest()
                ->paginate(8),
            'jumlahProyek' => $klien->proyek()->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge(['nama_klien' => trim((string) $request->input('nama_klien'))]);

        $validated = $request->validate([
            'nama_klien' => ['required', 'string', 'max:150', Rule::unique('klien', 'nama_klien')],
            'alamat' => ['nullable', 'string'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        Klien::create($validated);

        return Redirect::route('klien.index')->with('status', 'klien-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Klien $klien): View
    {
        return view('klien.edit', [
            'klien' => $klien,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Klien $klien)
    {
        $request->merge(['nama_klien' => trim((string) $request->input('nama_klien'))]);

        $validated = $request->validate([
            'nama_klien' => ['required', 'string', 'max:150', Rule::unique('klien', 'nama_klien')->ignore($klien->getKey())],
            'alamat' => ['nullable', 'string'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $klien->update($validated);

        return Redirect::route('klien.index')->with('status', 'klien-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Klien $klien)
    {
        if ($klien->proyek()->exists()) {
            return Redirect::route('klien.index')
                ->withErrors(['klien' => 'Klien tidak dapat dihapus karena masih digunakan oleh proyek.']);
        }

        $klien->delete();

        return Redirect::route('klien.index')->with('status', 'klien-deleted');
    }
}
