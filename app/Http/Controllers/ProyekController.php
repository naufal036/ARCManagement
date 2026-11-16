<?php

namespace App\Http\Controllers;

use App\Models\JenisPekerjaan;
use App\Models\Klien;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProyekController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('proyek.index', [
            'proyek' => Proyek::with(['klien', 'jenisPekerjaan'])->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('proyek.create', [
            'klien' => Klien::orderBy('nama_klien')->get(),
            'jenisPekerjaan' => JenisPekerjaan::orderBy('nama_pekerjaan')->get(),
        ]);
    }

    public function show(Proyek $proyek): View
    {
        return view('proyek.show', [
            'proyek' => $proyek->load([
                'klien',
                'jenisPekerjaan',
                'progressProyek' => fn ($query) => $query->latest('tanggal_laporan'),
            ]),
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
        ]);

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
        $proyek->delete();

        return Redirect::route('proyek.index')->with('status', 'proyek-deleted');
    }
}
