<?php

namespace App\Http\Controllers;

use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class JenisPekerjaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('jenis-pekerjaan.index', [
            'jenisPekerjaan' => JenisPekerjaan::latest()->paginate(10),
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
        return view('jenis-pekerjaan.show', compact('jenisPekerjaan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pekerjaan' => ['required', 'string', 'max:100'],
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
        $validated = $request->validate([
            'nama_pekerjaan' => ['required', 'string', 'max:100'],
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
        $jenisPekerjaan->delete();

        return Redirect::route('jenis-pekerjaan.index')->with('status', 'jenis-pekerjaan-deleted');
    }
}
