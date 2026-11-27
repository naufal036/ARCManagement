<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Proyek;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChecklistItemController extends Controller
{
    public function index(Proyek $proyek): View { $this->authorizeProject($proyek); return view('checklist.index', ['proyek' => $proyek, 'items' => ChecklistItem::whereBelongsTo($proyek)->whereBelongsTo(auth()->user(), 'user')->latest()->get()]); }

    public function store(Request $request, Proyek $proyek): RedirectResponse
    {
        $this->authorizeProject($proyek);
        $data = $request->validate(['nama_kegiatan' => ['required', 'string', 'max:255'], 'kategori' => ['nullable', 'string', 'max:100'], 'level_urgent' => ['required', 'in:Prioritas I,Prioritas II,Prioritas III,High Urgent,Low Urgent'], 'deadline' => ['nullable', 'date'], 'catatan' => ['nullable', 'string']]);
        $proyek->checklistItems()->create([...$data, 'user_id' => auth()->id()]);
        return back()->with('success', 'Checklist berhasil ditambahkan.');
    }

    public function update(Request $request, Proyek $proyek, ChecklistItem $checklist): RedirectResponse
    {
        abort_unless($checklist->proyek_id === $proyek->id && $checklist->user_id === auth()->id(), 403);
        $checklist->update($request->validate(['nama_kegiatan' => ['required', 'string', 'max:255'], 'kategori' => ['nullable', 'string', 'max:100'], 'level_urgent' => ['required', 'in:Prioritas I,Prioritas II,Prioritas III,High Urgent,Low Urgent'], 'deadline' => ['nullable', 'date'], 'catatan' => ['nullable', 'string'], 'is_completed' => ['boolean']]));
        return back()->with('success', 'Checklist diperbarui.');
    }

    public function destroy(Proyek $proyek, ChecklistItem $checklist): RedirectResponse { abort_unless($checklist->proyek_id === $proyek->id && $checklist->user_id === auth()->id(), 403); $checklist->delete(); return back()->with('success', 'Checklist dihapus.'); }

    private function authorizeProject(Proyek $proyek): void
    {
        $role = auth()->user()?->role?->name;
        $hasAssignment = $proyek->projectAssignments()->where('user_id', auth()->id())->exists();

        abort_unless(in_array($role, ['Admin', 'Direktur'], true) || $hasAssignment, 403, 'Anda tidak memiliki akses ke proyek ini.');
    }
}
