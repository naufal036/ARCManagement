<?php

namespace App\Http\Controllers;

use App\Models\LaporanPengawasan;
use App\Models\ProjectAssignment;
use App\Models\Proyek;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LaporanPengawasanController extends Controller
{
    public function index(Request $request): View
    {
        $projects = $this->accessibleProjects();
        $reports = LaporanPengawasan::with(['proyek', 'user'])
            ->whereIn('proyek_id', $projects->pluck('id'))
            ->when($request->integer('project_id'), fn (Builder $query, int $projectId) => $query->where('proyek_id', $projectId))
            ->latest('periode_mulai')
            ->paginate(12)
            ->withQueryString();

        return view('laporan-pengawasan.index', compact('reports', 'projects'));
    }

    public function create(Request $request): View
    {
        $projects = $this->accessibleProjects();
        $project = $projects->firstWhere('id', $request->integer('proyek_id'));

        abort_unless($project !== null, 404);
        Gate::authorize('create', [LaporanPengawasan::class, $project]);

        return view('laporan-pengawasan.create', compact('project'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $project = Proyek::findOrFail($data['proyek_id']);
        Gate::authorize('create', [LaporanPengawasan::class, $project]);
        $data['user_id'] = $request->user()->id;
        $report = LaporanPengawasan::create($data);

        return redirect()->route('laporan-pengawasan.show', $report)->with('success', 'Laporan pengawasan berhasil dibuat.');
    }

    public function show(LaporanPengawasan $laporanPengawasan): View
    {
        Gate::authorize('view', $laporanPengawasan);

        return view('laporan-pengawasan.show', ['report' => $laporanPengawasan->load(['proyek', 'user'])]);
    }

    public function edit(LaporanPengawasan $laporanPengawasan): View
    {
        Gate::authorize('update', $laporanPengawasan);

        return view('laporan-pengawasan.edit', ['report' => $laporanPengawasan->load('proyek')]);
    }

    public function update(Request $request, LaporanPengawasan $laporanPengawasan): RedirectResponse
    {
        Gate::authorize('update', $laporanPengawasan);
        $laporanPengawasan->update($request->validate($this->rules(false)));

        return redirect()->route('laporan-pengawasan.show', $laporanPengawasan)->with('success', 'Laporan pengawasan berhasil diperbarui.');
    }

    public function destroy(LaporanPengawasan $laporanPengawasan): RedirectResponse
    {
        Gate::authorize('delete', $laporanPengawasan);
        $laporanPengawasan->delete();

        return redirect()->route('laporan-pengawasan.index')->with('success', 'Laporan pengawasan berhasil dihapus.');
    }

    private function rules(bool $withProject = true): array
    {
        return [
            'proyek_id' => [$withProject ? 'required' : 'sometimes', 'exists:proyek,id'],
            'periode_mulai' => ['required', 'date'],
            'periode_selesai' => ['required', 'date', 'after_or_equal:periode_mulai'],
            'jenis_laporan' => ['required', 'string', 'max:100'],
            'nama_pengawas' => ['nullable', 'string', 'max:150'],
            'rencana_kegiatan' => ['nullable', 'string'],
            'kegiatan_selesai' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'inspection_checklist' => ['nullable', 'array'],
            'photo_references' => ['nullable', 'array'],
            'status' => ['required', 'in:Draft,Diajukan,Disetujui,Perlu Perbaikan'],
        ];
    }

    private function accessibleProjects()
    {
        $user = request()->user();

        return Proyek::query()
            ->where('project_type', 'Pengawasan')
            ->when(! in_array($user->role?->name, ['Admin', 'Direktur'], true), fn (Builder $query) => $query->whereIn('id', ProjectAssignment::query()->where('user_id', $user->id)->select('project_id')))
            ->orderBy('nama_proyek')
            ->get();
    }
}
