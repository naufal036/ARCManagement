<?php

namespace App\Http\Controllers;

use App\Models\Klien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class KlienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('klien.index', [
            'klien' => Klien::latest()->paginate(10),
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
        return view('klien.show', compact('klien'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_klien' => ['required', 'string', 'max:150'],
            'jenis_client' => ['required', 'in:Sekolah,Pemerintah,Swasta,Lainnya'],
            'npsn' => ['nullable', 'string', 'max:20'],
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
        $validated = $request->validate([
            'nama_klien' => ['required', 'string', 'max:150'],
            'jenis_client' => ['required', 'in:Sekolah,Pemerintah,Swasta,Lainnya'],
            'npsn' => ['nullable', 'string', 'max:20'],
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
        $klien->delete();

        return Redirect::route('klien.index')->with('status', 'klien-deleted');
    }
}
