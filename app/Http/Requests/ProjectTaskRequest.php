<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_assignment_id' => ['required', 'integer', 'exists:project_assignments,id'],
            'task_name' => ['required', 'string', 'max:255'],
            'task_description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
        ];
    }

    public function messages(): array
    {
        return [
            'project_assignment_id.required' => 'Penugasan proyek wajib dipilih.',
            'project_assignment_id.exists' => 'Penugasan proyek yang dipilih tidak tersedia.',
            'task_name.required' => 'Nama tugas wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'due_date.required' => 'Deadline wajib diisi.',
            'due_date.date' => 'Deadline tidak valid.',
            'due_date.after_or_equal' => 'Deadline tidak boleh sebelum tanggal mulai.',
            'priority.required' => 'Prioritas wajib dipilih.',
            'priority.in' => 'Prioritas tugas tidak valid.',
            'status.required' => 'Status tugas wajib dipilih.',
            'status.in' => 'Status tugas tidak valid.',
        ];
    }
}
