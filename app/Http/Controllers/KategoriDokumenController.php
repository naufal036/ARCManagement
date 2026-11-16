<?php

namespace App\Http\Controllers;

use App\Models\KategoriDokumen;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class KategoriDokumenController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', KategoriDokumen::class);

        return view('kategori-dokumen.index', [
            'kategoriDokumen' => KategoriDokumen::withCount('dokumenProyek')
                ->orderBy('nama_kategori')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', KategoriDokumen::class);

        return view('kategori-dokumen.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', KategoriDokumen::class);

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori_dokumen,nama_kategori'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ]);

        KategoriDokumen::create($validated);

        return redirect()->route('kategori-dokumen.index')->with('success', 'Kategori dokumen berhasil dibuat.');
    }

    public function edit(KategoriDokumen $kategoriDokumen): View
    {
        Gate::authorize('update', $kategoriDokumen);

        return view('kategori-dokumen.edit', compact('kategoriDokumen'));
    }

    public function update(Request $request, KategoriDokumen $kategoriDokumen): RedirectResponse
    {
        Gate::authorize('update', $kategoriDokumen);

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori_dokumen,nama_kategori,'.$kategoriDokumen->getKey()],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ]);

        $kategoriDokumen->update($validated);

        return redirect()->route('kategori-dokumen.index')->with('success', 'Kategori dokumen berhasil diperbarui.');
    }

    public function destroy(KategoriDokumen $kategoriDokumen): RedirectResponse
    {
        Gate::authorize('delete', $kategoriDokumen);

        if ($kategoriDokumen->dokumenProyek()->exists()) {
            return redirect()->route('kategori-dokumen.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan.');
        }

        $kategoriDokumen->delete();

        return redirect()->route('kategori-dokumen.index')->with('success', 'Kategori dokumen berhasil dihapus.');
    }
}
