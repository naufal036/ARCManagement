<?php

namespace App\Http\Controllers;

use App\Models\ProjectAssignment;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectAssignmentController extends Controller
{
    public function index()
    {
        $assignments = ProjectAssignment::with(['proyek', 'user'])->latest()->get();
        return view('project-assignment.index', compact('assignments'));
    }

    public function create()
    {
        $proyeks = Proyek::all();
        $users = User::all();
        return view('project-assignment.create', compact('proyeks', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:proyek,id',
            'user_id' => 'required|exists:users,id',
            'role_in_project' => 'required|string|max:255',
            'assigned_date' => 'required|date',
        ]);

        ProjectAssignment::create($validated);

        return redirect()->route('project-assignment.index')->with('success', 'Penugasan tim berhasil ditambahkan.');
    }

    public function show(ProjectAssignment $projectAssignment)
    {
        $projectAssignment->load(['proyek', 'user', 'projectTasks']);
        return view('project-assignment.show', compact('projectAssignment'));
    }

    public function edit(ProjectAssignment $projectAssignment)
    {
        $proyeks = Proyek::all();
        $users = User::all();
        return view('project-assignment.edit', compact('projectAssignment', 'proyeks', 'users'));
    }

    public function update(Request $request, ProjectAssignment $projectAssignment)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:proyek,id',
            'user_id' => 'required|exists:users,id',
            'role_in_project' => 'required|string|max:255',
            'assigned_date' => 'required|date',
        ]);

        $projectAssignment->update($validated);

        return redirect()->route('project-assignment.index')->with('success', 'Penugasan tim berhasil diperbarui.');
    }

    public function destroy(ProjectAssignment $projectAssignment)
    {
        $projectAssignment->delete();

        return redirect()->route('project-assignment.index')->with('success', 'Penugasan tim berhasil dihapus.');
    }
}