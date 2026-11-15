<?php

namespace App\Http\Controllers;

use App\Models\KategoriDokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriDokumenController extends Controller
{
    public function index(): View
    {
        return view('kategori-dokumen.index', [
            'kategoriDokumen' => KategoriDokumen::withCount('dokumenProyek')
                ->orderBy('nama_kategori')
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('kategori-dokumen.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori_dokumen,nama_kategori'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        KategoriDokumen::create($validated);

        return redirect()->route('kategori-dokumen.index')->with('status', 'kategori-dokumen-created');
    }

    public function edit(KategoriDokumen $kategoriDokumen): View
    {
        return view('kategori-dokumen.edit', [
            'kategoriDokumen' => $kategoriDokumen,
        ]);
    }

    public function update(Request $request, KategoriDokumen $kategoriDokumen): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori_dokumen,nama_kategori,'.$kategoriDokumen->id],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $kategoriDokumen->update($validated);

        return redirect()->route('kategori-dokumen.index')->with('status', 'kategori-dokumen-updated');
    }

    public function destroy(KategoriDokumen $kategoriDokumen): RedirectResponse
    {
        if ($kategoriDokumen->dokumenProyek()->exists()) {
            return redirect()->route('kategori-dokumen.index')
                ->with('status', 'kategori-dokumen-in-use');
        }

        $kategoriDokumen->delete();

        return redirect()->route('kategori-dokumen.index')->with('status', 'kategori-dokumen-deleted');
    }
}
