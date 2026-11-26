<?php

namespace App\Http\Requests;

use App\Models\Contract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $value = $this->input('contract_value');

        if (is_string($value)) {
            $value = trim($value);

            if (str_contains($value, ',')) {
                $value = str_replace(['.', ','], ['', '.'], $value);
            } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $value)) {
                $value = str_replace('.', '', $value);
            }

            $this->merge(['contract_value' => $value]);
        }
    }

    public function rules(): array
    {
        $contract = $this->route('contract');

        return [
            'project_id' => ['required', 'integer', 'exists:proyek,id'],
            'contract_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('contracts', 'contract_number')
                    ->ignore($contract instanceof Contract ? $contract->getKey() : $contract),
            ],
            'contract_name' => ['required', 'string', 'max:255'],
            'contract_date' => ['required', 'date'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'contract_value' => ['required', 'numeric', 'decimal:0,2', 'gt:0', 'max:9999999999999.99'],
            'status' => ['required', Rule::in(['draft', 'active', 'completed', 'terminated'])],
            'file_kontrak' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Proyek wajib dipilih.',
            'project_id.exists' => 'Proyek yang dipilih tidak tersedia.',
            'contract_number.required' => 'Nomor kontrak wajib diisi.',
            'contract_number.max' => 'Nomor kontrak maksimal 255 karakter.',
            'contract_number.unique' => 'Nomor kontrak sudah digunakan.',
            'contract_name.required' => 'Nama kontrak wajib diisi.',
            'contract_date.required' => 'Tanggal kontrak wajib diisi.',
            'contract_date.date' => 'Tanggal kontrak tidak valid.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Tanggal selesai tidak valid.',
            'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'contract_value.required' => 'Nilai kontrak wajib diisi.',
            'contract_value.numeric' => 'Nilai kontrak harus berupa angka.',
            'contract_value.decimal' => 'Nilai kontrak maksimal memiliki 2 angka desimal.',
            'contract_value.gt' => 'Nilai kontrak harus lebih besar dari 0.',
            'contract_value.max' => 'Nilai kontrak tidak boleh melebihi 9.999.999.999.999,99.',
            'status.required' => 'Status kontrak wajib dipilih.',
            'status.in' => 'Status kontrak tidak valid.',
            'file_kontrak.file' => 'File kontrak tidak valid.',
            'file_kontrak.mimes' => 'File kontrak harus berformat PDF.',
            'file_kontrak.max' => 'Ukuran file kontrak maksimal 5 MB.',
        ];
    }
}
