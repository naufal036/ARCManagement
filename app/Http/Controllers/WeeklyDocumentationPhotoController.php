<?php

namespace App\Http\Controllers;

use App\Models\WeeklyDocumentation;
use App\Models\WeeklyDocumentationPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class WeeklyDocumentationPhotoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'weekly_documentation_id' => ['required', 'exists:weekly_documentations,id'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $weekly = WeeklyDocumentation::findOrFail($data['weekly_documentation_id']);
        $this->authorizeWeekly($weekly);
        $weekly->photos()->create([
            'description' => $data['description'],
            'path' => $request->file('image')->store('weekly-documentation', 'private'),
            'is_mandatory' => false,
        ]);

        return redirect()->route('weekly-documentation.show', $weekly)->with('success', 'Foto tambahan berhasil diunggah.');
    }

    public function update(Request $request, WeeklyDocumentation $weeklyDocumentation): RedirectResponse
    {
        $this->authorizeWeekly($weeklyDocumentation);
        $request->validate(['photos' => ['required', 'array'], 'photos.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'photos.*.description' => ['nullable', 'string', 'max:255'], 'details' => ['nullable', 'array'], 'details.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'details.*.description' => ['nullable', 'string', 'max:255']]);
        foreach ($weeklyDocumentation->photos as $photo) { $photo->update(['description' => $request->input("photos.{$photo->id}.description")]); if ($request->hasFile("photos.{$photo->id}.image")) { if ($photo->path) Storage::disk('private')->delete($photo->path); $photo->update(['path' => $request->file("photos.{$photo->id}.image")->store('weekly-documentation', 'private')]); } }
        foreach ($request->input('details', []) as $key => $detail) if ($request->hasFile("details.{$key}.image")) $weeklyDocumentation->photos()->create(['description' => $detail['description'] ?? null, 'path' => $request->file("details.{$key}.image")->store('weekly-documentation', 'private'), 'is_mandatory' => false]);
        return back()->with('success', 'Dokumentasi mingguan disimpan.');
    }

    public function view(WeeklyDocumentationPhoto $photo): Response
    {
        $this->authorizeWeekly($photo->weeklyDocumentation);
        abort_if($photo->path === null || ! Storage::disk('private')->exists($photo->path), 404);

        return response()->file(Storage::disk('private')->path($photo->path));
    }

    public function updateSingle(Request $request, WeeklyDocumentationPhoto $photo): RedirectResponse
    {
        $this->authorizeWeekly($photo->weeklyDocumentation);
        abort_if($photo->is_mandatory, 403, 'Gunakan form foto wajib untuk memperbarui slot mandatory.');
        $data = $request->validate(['description' => ['required', 'string', 'max:255'], 'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']]);
        if ($request->hasFile('image')) {
            Storage::disk('private')->delete($photo->path);
            $data['path'] = $request->file('image')->store('weekly-documentation', 'private');
        }
        $photo->update($data);

        return back()->with('success', 'Foto tambahan diperbarui.');
    }

    public function destroy(WeeklyDocumentationPhoto $photo): RedirectResponse
    {
        $this->authorizeWeekly($photo->weeklyDocumentation);
        abort_if($photo->is_mandatory, 403, 'Foto wajib tidak dapat dihapus.');
        $weekly = $photo->weeklyDocumentation;
        Storage::disk('private')->delete($photo->path);
        $photo->delete();

        return redirect()->route('weekly-documentation.show', $weekly)->with('success', 'Foto tambahan dihapus.');
    }

    private function authorizeWeekly(WeeklyDocumentation $weekly): void
    {
        $role = auth()->user()?->role?->name;
        $isManager = in_array($role, ['Admin', 'Direktur'], true);
        $hasAssignment = $weekly->loadMissing('proyek')->proyek?->projectAssignments()
            ->where('user_id', auth()->id())
            ->exists();

        abort_unless($isManager || $hasAssignment, 403, 'Anda tidak memiliki akses ke dokumentasi proyek ini.');
    }
}
