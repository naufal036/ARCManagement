<?php

namespace App\Http\Controllers;

use App\Models\ProgressProyek;
use Illuminate\Http\Request;

class ProgressProyekController extends Controller
{
    // Menampilkan daftar laporan progress proyek
    public function index()
    {
        $progressProyek = ProgressProyek::with('user')
            ->latest('tanggal_laporan')
            ->get();

        return view('progress-proyek.index', compact('progressProyek'));
    }

    // Menampilkan form untuk membuat laporan progress proyek baru
    public function create()
    {
        return view('progress-proyek.create');
    }

    // Menyimpan laporan progress proyek baru ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_laporan' => 'required|date',
            'persentase_progress' => 'required|numeric|min:0|max:100',
            'uraian_pekerjaan' => 'required|string',
            'kendala' => 'nullable|string',
            'solusi' => 'nullable|string',
            'catatan' => 'nullable|string',
            'rencana_selanjutnya' => 'nullable|string',
        ]);

        $validated['proyek_id'] = null;
        $validated['user_id'] = auth()->id();

        $progress = ProgressProyek::create($validated);

        return redirect()
            ->route('progress-proyek.show', $progress->id)
            ->with('success', 'Laporan progress berhasil disimpan.');
    }

    // Menampilkan detail laporan progress proyek tertentu
    public function show(string $id)
    {
        $progress = ProgressProyek::with('user')
            ->findOrFail($id);

        return view('progress-proyek.show', compact('progress'));
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(string $id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
