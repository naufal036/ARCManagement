<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectAssignmentRequest;
use App\Models\ProjectAssignment;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProjectAssignment::with(['proyek', 'user'])->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return view('project-assignment.index', [
            'assignments' => $query->paginate(10)->withQueryString(),
            'proyeks' => Proyek::orderBy('nama_proyek')->get(),
            'filters' => $request->only(['project_id', 'status']),
        ]);
    }

    public function create(): View
    {
        $proyeks = Proyek::orderBy('nama_proyek')->get();
        $users = User::orderBy('name')->get();

        return view('project-assignment.create', compact('proyeks', 'users'));
    }

    public function store(ProjectAssignmentRequest $request)
    {
        $assignment = ProjectAssignment::create($request->validated());

        return redirect()->route('project-assignment.show', $assignment)->with('success', 'Penugasan tim berhasil ditambahkan.');
    }

    public function show(ProjectAssignment $projectAssignment): View
    {
        $projectAssignment->load(['proyek', 'user', 'projectTasks']);

        return view('project-assignment.show', compact('projectAssignment'));
    }

    public function edit(ProjectAssignment $projectAssignment): View
    {
        $proyeks = Proyek::orderBy('nama_proyek')->get();
        $users = User::orderBy('name')->get();

        return view('project-assignment.edit', compact('projectAssignment', 'proyeks', 'users'));
    }

    public function update(ProjectAssignmentRequest $request, ProjectAssignment $projectAssignment)
    {
        $projectAssignment->update($request->validated());

        return redirect()->route('project-assignment.show', $projectAssignment)->with('success', 'Penugasan tim berhasil diperbarui.');
    }

    public function destroy(ProjectAssignment $projectAssignment)
    {
        $projectAssignment->delete();

        return redirect()->route('project-assignment.index')->with('success', 'Penugasan tim berhasil dihapus.');
    }
}
