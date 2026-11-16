<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Proyek;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::with('proyek')->latest()->get();

        return view('contract.index', compact('contracts'));
    }

    public function create()
    {
        $proyeks = Proyek::all();

        return view('contract.create', compact('proyeks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:proyek,id',
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number',
            'contract_name' => 'required|string|max:255',
            'contract_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'contract_value' => 'required|numeric|min:0',
            'status' => 'required|string|max:255',
        ]);

        Contract::create($validated);

        return redirect()->route('contract.index')->with('success', 'Kontrak berhasil ditambahkan.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['proyek', 'contractAddendums']);

        return view('contract.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $proyeks = Proyek::all();

        return view('contract.edit', compact('contract', 'proyeks'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:proyek,id',
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number,'.$contract->id,
            'contract_name' => 'required|string|max:255',
            'contract_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'contract_value' => 'required|numeric|min:0',
            'status' => 'required|string|max:255',
        ]);

        $contract->update($validated);

        return redirect()->route('contract.index')->with('success', 'Kontrak berhasil diperbarui.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contract.index')->with('success', 'Kontrak berhasil dihapus.');
    }
}
