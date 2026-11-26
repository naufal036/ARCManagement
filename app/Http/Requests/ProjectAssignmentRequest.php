<?php

namespace App\Http\Requests;

use App\Models\ProjectAssignment;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $assignment = $this->route('projectAssignment') ?? $this->route('project_assignment');
        $isActive = $this->input('status', $assignment instanceof ProjectAssignment ? $assignment->status : 'active') === 'active';
        $userRules = ['exists:users,id'];

        if ($isActive) {
            $uniqueRule = Rule::unique('project_assignments', 'user_id')
                ->where(fn (Builder $query) => $query
                    ->where('project_id', $this->input('project_id'))
                    ->where('status', 'active'));

            if ($assignment instanceof ProjectAssignment) {
                $uniqueRule->ignore($assignment->getKey());
            }

            $userRules[] = $uniqueRule;
        }

        return [
            'project_id' => ['required', 'integer', 'exists:proyek,id'],
            'user_id' => array_merge(['required', 'integer'], $userRules),
            'role_in_project' => ['required', 'string', 'max:255'],
            'assigned_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'completed', 'cancelled'])],
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Proyek wajib dipilih.',
            'project_id.exists' => 'Proyek yang dipilih tidak tersedia.',
            'user_id.required' => 'Anggota wajib dipilih.',
            'user_id.exists' => 'Anggota tidak tersedia atau sudah memiliki penugasan aktif di proyek ini.',
            'user_id.unique' => 'Anggota sudah memiliki penugasan aktif di proyek ini.',
            'role_in_project.required' => 'Posisi di proyek wajib diisi.',
            'assigned_date.required' => 'Tanggal penugasan wajib diisi.',
            'assigned_date.date' => 'Tanggal penugasan tidak valid.',
            'status.required' => 'Status penugasan wajib dipilih.',
            'status.in' => 'Status penugasan tidak valid.',
        ];
    }
}
