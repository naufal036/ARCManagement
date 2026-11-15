<?php

namespace App\Http\Controllers;

use App\Models\DokumenProyek;
use App\Models\KategoriDokumen;
use App\Models\Proyek;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DokumenProyekController extends Controller
{
    public function index(): View
    {
        return view('dokumen-proyek.index', [
            'dokumenProyek' => DokumenProyek::with(['proyek', 'kategoriDokumen', 'versiTerbaru'])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('dokumen-proyek.create', [
            'kategoriDokumen' => KategoriDokumen::orderBy('nama_kategori')->get(),
            'proyek' => Proyek::orderBy('nama_proyek')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'proyek_id' => ['required', 'exists:proyek,id'],
            'kategori_dokumen_id' => ['required', 'exists:kategori_dokumen,id'],
            'nama_dokumen' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $dokumenProyek = DokumenProyek::create($validated);

        return redirect()->route('dokumen-proyek.show', $dokumenProyek)
            ->with('status', 'dokumen-proyek-created');
    }

    public function show(DokumenProyek $dokumenProyek): View
    {
        return view('dokumen-proyek.show', [
            'dokumenProyek' => $dokumenProyek->load([
                'proyek',
                'kategoriDokumen',
                'versiDokumen.pengunggah',
            ]),
        ]);
    }

    public function edit(DokumenProyek $dokumenProyek): View
    {
        return view('dokumen-proyek.edit', [
            'dokumenProyek' => $dokumenProyek,
            'kategoriDokumen' => KategoriDokumen::orderBy('nama_kategori')->get(),
            'proyek' => Proyek::orderBy('nama_proyek')->get(),
        ]);
    }

    public function update(Request $request, DokumenProyek $dokumenProyek): RedirectResponse
    {
        $validated = $request->validate([
            'proyek_id' => ['required', 'exists:proyek,id'],
            'kategori_dokumen_id' => ['required', 'exists:kategori_dokumen,id'],
            'nama_dokumen' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $dokumenProyek->update($validated);

        return redirect()->route('dokumen-proyek.show', $dokumenProyek)
            ->with('status', 'dokumen-proyek-updated');
    }

    public function destroy(DokumenProyek $dokumenProyek): RedirectResponse
    {
        $paths = $dokumenProyek->versiDokumen()->pluck('path_file')->all();
        $dokumenProyek->delete();

        if ($paths !== [] && ! Storage::disk('local')->delete($paths)) {
            return redirect()->route('dokumen-proyek.index')
                ->with('status', 'dokumen-proyek-deleted-files-retained');
        }

        return redirect()->route('dokumen-proyek.index')->with('status', 'dokumen-proyek-deleted');
    }
}
