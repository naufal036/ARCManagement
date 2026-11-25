<?php

namespace App\Http\Requests;

use App\Models\Contract;
use App\Models\ContractAddendum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractAddendumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $contract = $this->route('contract');

        if ($contract instanceof Contract) {
            $this->merge(['contract_id' => $contract->getKey()]);
        }
    }

    public function rules(): array
    {
        $contract = $this->route('contract');
        $addendum = $this->route('contractAddendum');
        $startDate = $contract instanceof Contract
            ? $contract->start_date->toDateString()
            : now()->toDateString();

        return [
            'contract_id' => ['required', 'integer', 'exists:contracts,id'],
            'addendum_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('contract_addendums', 'addendum_number')
                    ->ignore($addendum instanceof ContractAddendum ? $addendum->getKey() : $addendum),
            ],
            'addendum_date' => ['required', 'date'],
            'value_after' => ['required', 'numeric', 'gte:0'],
            'new_end_date' => ['nullable', 'date', 'after:'.$startDate],
            'description' => ['nullable', 'string'],
            'file_addendum' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'contract_id.required' => 'Kontrak wajib dipilih.',
            'contract_id.exists' => 'Kontrak yang dipilih tidak tersedia.',
            'addendum_number.required' => 'Nomor addendum wajib diisi.',
            'addendum_number.unique' => 'Nomor addendum sudah digunakan.',
            'addendum_date.required' => 'Tanggal addendum wajib diisi.',
            'addendum_date.date' => 'Tanggal addendum tidak valid.',
            'value_after.required' => 'Nilai sesudah addendum wajib diisi.',
            'value_after.numeric' => 'Nilai sesudah addendum harus berupa angka.',
            'value_after.gte' => 'Nilai setelah addendum tidak boleh kurang dari 0.',
            'new_end_date.date' => 'Tanggal selesai baru tidak valid.',
            'new_end_date.after' => 'Tanggal selesai baru harus setelah tanggal mulai kontrak.',
            'file_addendum.file' => 'File addendum tidak valid.',
            'file_addendum.mimes' => 'File addendum harus berformat PDF.',
            'file_addendum.max' => 'Ukuran file addendum maksimal 5 MB.',
        ];
    }
}
