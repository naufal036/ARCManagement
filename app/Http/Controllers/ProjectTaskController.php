<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectTaskRequest;
use App\Models\ProjectAssignment;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectTaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProjectTask::with(['projectAssignment.proyek', 'projectAssignment.user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }

        if ($request->filled('project_assignment_id')) {
            $query->where('project_assignment_id', $request->integer('project_assignment_id'));
        }

        return view('project-task.index', [
            'tasks' => $query->paginate(10)->withQueryString(),
            'assignments' => ProjectAssignment::with(['proyek', 'user'])->orderByDesc('assigned_date')->get(),
            'filters' => $request->only(['status', 'priority', 'project_assignment_id']),
        ]);
    }

    public function create(): View
    {
        return view('project-task.create', [
            'assignments' => ProjectAssignment::with(['proyek', 'user'])->orderByDesc('assigned_date')->get(),
        ]);
    }

    public function store(ProjectTaskRequest $request)
    {
        $task = ProjectTask::create($request->validated());

        return redirect()->route('project-task.show', $task)->with('success', 'Tugas proyek berhasil ditambahkan.');
    }

    public function show(ProjectTask $projectTask): View
    {
        $projectTask->load(['projectAssignment.proyek', 'projectAssignment.user']);

        return view('project-task.show', compact('projectTask'));
    }

    public function edit(ProjectTask $projectTask): View
    {
        return view('project-task.edit', [
            'projectTask' => $projectTask,
            'assignments' => ProjectAssignment::with(['proyek', 'user'])->orderByDesc('assigned_date')->get(),
        ]);
    }

    public function update(ProjectTaskRequest $request, ProjectTask $projectTask)
    {
        $projectTask->update($request->validated());

        return redirect()->route('project-task.show', $projectTask)->with('success', 'Tugas proyek berhasil diperbarui.');
    }

    public function destroy(ProjectTask $projectTask)
    {
        $projectTask->delete();

        return redirect()->route('project-task.index')->with('success', 'Tugas proyek berhasil dihapus.');
    }
}
