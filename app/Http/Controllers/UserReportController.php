<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\ProjectAssignment;
use App\Models\UserReport;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['status' => ['nullable', 'in:All Things,In Progress,On Review,Need Changes,Completed'], 'month' => ['nullable', 'date_format:Y-m']]);
        $status = $filters['status'] ?? null;
        $month = CarbonImmutable::createFromFormat('Y-m', $filters['month'] ?? now()->format('Y-m'))->startOfMonth();
        $reports = UserReport::with('proyek')->whereBelongsTo(auth()->user())->when($status && $status !== 'All Things', fn ($query) => $query->where('status', $status))->latest('due_date')->get();
        return view('reports.index', ['reports' => $reports, 'projects' => $this->accessibleProjects(), 'statuses' => ['All Things', 'In Progress', 'On Review', 'Need Changes', 'Completed'], 'selectedStatus' => $status ?: 'All Things', 'month' => $month, 'calendarDays' => collect(range(1, $month->daysInMonth))->map(fn (int $day): CarbonImmutable => $month->setDay($day)), 'leadingDays' => $month->dayOfWeekIso - 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'proyek_id' => ['nullable', 'exists:proyek,id'], 'status' => ['required', 'in:In Progress,On Review,Need Changes,Completed'], 'priority' => ['required', 'in:Prioritas I,Prioritas II,Prioritas III,High Urgent,Low Urgent'], 'due_date' => ['nullable', 'date'], 'notes' => ['nullable', 'string'], 'is_completed' => ['boolean']]);
        $this->authorizeProject($data['proyek_id'] ?? null);
        $data['user_id'] = auth()->id(); $data['is_completed'] = ($data['status'] ?? null) === 'Completed' || (bool) ($data['is_completed'] ?? false); $data['status'] = $data['is_completed'] ? 'Completed' : $data['status']; UserReport::create($data);
        return back()->with('success', 'Report berhasil ditambahkan.');
    }

    public function update(Request $request, UserReport $report): RedirectResponse
    {
        abort_unless($report->user_id === auth()->id(), 403); $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'proyek_id' => ['nullable', 'exists:proyek,id'], 'status' => ['required', 'in:In Progress,On Review,Need Changes,Completed'], 'priority' => ['required', 'in:Prioritas I,Prioritas II,Prioritas III,High Urgent,Low Urgent'], 'due_date' => ['nullable', 'date'], 'notes' => ['nullable', 'string'], 'is_completed' => ['boolean']]); $this->authorizeProject($data['proyek_id'] ?? null); $data['is_completed'] = ($data['status'] ?? null) === 'Completed' || (bool) ($data['is_completed'] ?? false); $data['status'] = $data['is_completed'] ? 'Completed' : $data['status']; $report->update($data); return back()->with('success', 'Report diperbarui.');
    }

    public function destroy(UserReport $report): RedirectResponse { abort_unless($report->user_id === auth()->id(), 403); $report->delete(); return back()->with('success', 'Report dihapus.'); }

    private function accessibleProjects()
    {
        $role = auth()->user()?->role?->name;

        return Proyek::query()
            ->when(! in_array($role, ['Admin', 'Direktur'], true), fn ($query) => $query->whereIn('id', ProjectAssignment::query()->where('user_id', auth()->id())->select('project_id')))
            ->orderBy('nama_proyek')
            ->get(['id', 'nama_proyek']);
    }

    private function authorizeProject(?int $projectId): void
    {
        if ($projectId === null) {
            return;
        }

        abort_unless($this->accessibleProjects()->contains('id', $projectId), 403, 'Anda tidak memiliki akses ke proyek ini.');
    }
}
