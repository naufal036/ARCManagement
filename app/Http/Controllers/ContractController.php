<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Models\Contract;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractController extends Controller
{
    public function index(Request $request): View
    {
        $query = Contract::with('proyek')->latest();
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('contract_number', 'like', '%'.$search.'%')
                    ->orWhere('contract_name', 'like', '%'.$search.'%')
                    ->orWhereHas('proyek', fn ($projectQuery) => $projectQuery->where('nama_proyek', 'like', '%'.$search.'%'));
            });
        }

        return view('contract.index', [
            'contracts' => $query->paginate(10)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $proyeks = Proyek::orderBy('nama_proyek')->get();

        return view('contract.create', compact('proyeks'));
    }

    public function store(ContractRequest $request)
    {
        $validated = $request->validated();
        $file = $validated['file_kontrak'] ?? null;
        unset($validated['file_kontrak']);

        if ($file !== null) {
            $path = $file->store('contracts', 'local');
            if ($path === false) {
                return back()->withInput()->withErrors(['file_kontrak' => 'File kontrak gagal disimpan.']);
            }

            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
        }

        $contract = Contract::create($validated);

        return redirect()->route('contract.show', $contract)->with('success', 'Kontrak berhasil ditambahkan.');
    }

    public function show(Contract $contract): View
    {
        $contract->load(['proyek', 'contractAddendums' => fn ($query) => $query->latest('addendum_date')]);

        return view('contract.show', compact('contract'));
    }

    public function edit(Contract $contract): View
    {
        $proyeks = Proyek::orderBy('nama_proyek')->get();

        return view('contract.edit', compact('contract', 'proyeks'));
    }

    public function update(ContractRequest $request, Contract $contract)
    {
        $validated = $request->validated();
        $file = $validated['file_kontrak'] ?? null;
        unset($validated['file_kontrak']);

        if ($file !== null) {
            $path = $file->store('contracts', 'local');
            if ($path === false) {
                return back()->withInput()->withErrors(['file_kontrak' => 'File kontrak gagal disimpan.']);
            }

            $oldPath = $contract->file_path;
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $contract->update($validated);

            if ($oldPath !== null && ! Storage::disk('local')->delete($oldPath)) {
                return redirect()->route('contract.show', $contract)
                    ->with('error', 'Kontrak diperbarui, tetapi file lama gagal dihapus.');
            }
        } else {
            $contract->update($validated);
        }

        return redirect()->route('contract.show', $contract)->with('success', 'Kontrak berhasil diperbarui.');
    }

    public function destroy(Contract $contract)
    {
        if ($contract->contractAddendums()->exists()) {
            return redirect()->route('contract.show', $contract)
                ->with('error', 'Kontrak tidak dapat dihapus karena masih memiliki addendum.');
        }

        if ($contract->file_path !== null && ! Storage::disk('local')->delete($contract->file_path)) {
            return redirect()->route('contract.show', $contract)
                ->with('error', 'File kontrak gagal dihapus. Kontrak tidak dihapus.');
        }

        $contract->delete();

        return redirect()->route('contract.index')->with('success', 'Kontrak berhasil dihapus.');
    }

    public function download(Contract $contract): StreamedResponse
    {
        abort_unless(
            $contract->file_path !== null && Storage::disk('local')->exists($contract->file_path),
            404
        );

        return Storage::disk('local')->download(
            $contract->file_path,
            $contract->file_name ?? basename($contract->file_path)
        );
    }
}
