<?php

namespace App\Http\Controllers;

use App\Models\DokumenProyek;
use App\Models\VersiDokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VersiDokumenController extends Controller
{
    public function store(Request $request, DokumenProyek $dokumenProyek): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png', 'max:20480'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        $file = $request->file('file');
        $nomorVersi = ((int) $dokumenProyek->versiDokumen()->max('nomor_versi')) + 1;
        $namaSimpan = Str::uuid().'.'.$file->extension();
        $path = $file->storeAs('dokumen-proyek/'.$dokumenProyek->id, $namaSimpan, 'local');

        if ($path === false) {
            throw new \RuntimeException('File dokumen tidak berhasil disimpan.');
        }

        $dokumenProyek->versiDokumen()->create([
            'nomor_versi' => $nomorVersi,
            'nama_file' => $file->getClientOriginalName(),
            'path_file' => $path,
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'ukuran_file' => $file->getSize(),
            'catatan' => $validated['catatan'] ?? null,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('dokumen-proyek.show', $dokumenProyek)
            ->with('status', 'versi-dokumen-created');
    }

    public function download(VersiDokumen $versiDokumen): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($versiDokumen->path_file), 404);

        return Storage::disk('local')->download(
            $versiDokumen->path_file,
            $versiDokumen->nama_file
        );
    }
}
