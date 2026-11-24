<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractAddendumRequest;
use App\Models\Contract;
use App\Models\ContractAddendum;
use Illuminate\Support\Facades\Storage;

class ContractAddendumController extends Controller
{
    public function store(ContractAddendumRequest $request, Contract $contract)
    {
        $validated = $request->validated();
        $file = $validated['file_addendum'] ?? null;
        unset($validated['file_addendum']);

        $validated['contract_id'] = $contract->id;
        $validated['value_before'] = $contract->contract_value;
        $validated['additional_value'] = (float) $validated['value_after'] - (float) $contract->contract_value;

        if ($file !== null) {
            $path = $file->store('contract-addendums', 'local');
            if ($path === false) {
                return back()->withInput()->withErrors(['file_addendum' => 'File addendum gagal disimpan.']);
            }

            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
        }

        $contract->contractAddendums()->create($validated);

        return redirect()->route('contract.show', $contract)->with('success', 'Addendum berhasil ditambahkan.');
    }

    public function update(
        ContractAddendumRequest $request,
        Contract $contract,
        ContractAddendum $contractAddendum
    ) {
        $addendum = $contract->contractAddendums()->findOrFail($contractAddendum->id);
        $validated = $request->validated();
        $file = $validated['file_addendum'] ?? null;
        unset($validated['file_addendum'], $validated['contract_id']);

        $validated['value_before'] = $contract->contract_value;
        $validated['additional_value'] = (float) $validated['value_after'] - (float) $contract->contract_value;

        if ($file !== null) {
            $path = $file->store('contract-addendums', 'local');
            if ($path === false) {
                return back()->withInput()->withErrors(['file_addendum' => 'File addendum gagal disimpan.']);
            }

            $oldPath = $addendum->file_path;
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $addendum->update($validated);

            if ($oldPath !== null && ! Storage::disk('local')->delete($oldPath)) {
                return redirect()->route('contract.show', $contract)
                    ->with('error', 'Addendum diperbarui, tetapi file lama gagal dihapus.');
            }
        } else {
            $addendum->update($validated);
        }

        return redirect()->route('contract.show', $contract)->with('success', 'Addendum berhasil diperbarui.');
    }

    public function destroy(Contract $contract, ContractAddendum $contractAddendum)
    {
        $addendum = $contract->contractAddendums()->findOrFail($contractAddendum->id);

        if ($addendum->file_path !== null && ! Storage::disk('local')->delete($addendum->file_path)) {
            return redirect()->route('contract.show', $contract)
                ->with('error', 'File addendum gagal dihapus. Addendum tidak dihapus.');
        }

        $addendum->delete();

        return redirect()->route('contract.show', $contract)->with('success', 'Addendum berhasil dihapus.');
    }

    public function download(Contract $contract, ContractAddendum $contractAddendum)
    {
        $addendum = $contract->contractAddendums()->findOrFail($contractAddendum->id);
        abort_unless(
            $addendum->file_path !== null && Storage::disk('local')->exists($addendum->file_path),
            404
        );

        return Storage::disk('local')->download(
            $addendum->file_path,
            $addendum->file_name ?? basename($addendum->file_path)
        );
    }
}
