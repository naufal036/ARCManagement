<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\WeeklyDocumentation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class WeeklyDocumentationController extends Controller
{
    public function index(Request $request): View { $projects = $this->accessibleProjects(); $proyek = $projects->firstWhere('id', $request->integer('project_id')); return view('weekly-documentation.index', ['projects' => $projects, 'selectedProject' => $proyek]); }
    public function store(Request $request, Proyek $proyek): RedirectResponse { $this->authorizeProject($proyek); $data = $request->validate(['week_number' => ['required', 'integer', 'min:1', 'max:9999']]); $weekly = WeeklyDocumentation::firstOrCreate(['proyek_id' => $proyek->id, 'week_number' => $data['week_number']], ['created_by' => auth()->id()]); if ($weekly->wasRecentlyCreated) $weekly->photos()->createMany(collect(range(1, 4))->map(fn (int $slot): array => ['slot_index' => $slot, 'is_mandatory' => true])->all()); return redirect()->route('weekly-documentation.show', $weekly); }
    public function show(WeeklyDocumentation $weeklyDocumentation): View { abort_unless($this->canAccess($weeklyDocumentation->load('proyek')->proyek), 403); return view('weekly-documentation.show', ['weekly' => $weeklyDocumentation->load('proyek', 'photos')]); }

    private function accessibleProjects(): Collection
    {
        $role = auth()->user()?->role?->name;
        return Proyek::with(['weeklyDocumentations.photos'])->when(! in_array($role, ['Admin', 'Direktur'], true), fn ($query) => $query->whereHas('projectAssignments', fn ($assignment) => $assignment->where('user_id', auth()->id())))->orderBy('nama_proyek')->get();
    }

    private function authorizeProject(Proyek $proyek): void { abort_unless($this->canAccess($proyek), 403, 'Anda tidak memiliki akses ke proyek ini.'); }
    private function canAccess(?Proyek $proyek): bool { $role = auth()->user()?->role?->name; return $proyek !== null && (in_array($role, ['Admin', 'Direktur'], true) || $proyek->projectAssignments()->where('user_id', auth()->id())->exists()); }
}
