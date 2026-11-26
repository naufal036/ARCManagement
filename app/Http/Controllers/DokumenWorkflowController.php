<?php

namespace App\Http\Controllers;

use App\Enums\DokumenVerificationStatus;
use App\Models\DokumenProyek;
use App\Services\DokumenWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DokumenWorkflowController extends Controller
{
    public function __construct(private readonly DokumenWorkflowService $workflowService) {}

    public function submit(Request $request, DokumenProyek $dokumenProyek): RedirectResponse
    {
        $validated = $request->validate([
            'lock_version' => ['required', 'integer', 'min:0'],
        ]);

        $this->workflowService->submitForReview($dokumenProyek, (int) $validated['lock_version']);

        return redirect()->route('dokumen-proyek.show', $dokumenProyek)
            ->with('success', 'Dokumen berhasil diajukan untuk pemeriksaan.');
    }

    public function review(Request $request, DokumenProyek $dokumenProyek): RedirectResponse
    {
        $validated = $request->validate([
            'lock_version' => ['required', 'integer', 'min:0'],
            'decision' => ['required', Rule::in(['pass', 'revision_required', 'rejected'])],
            'note' => ['nullable', 'string', 'max:2000', 'required_if:decision,revision_required,rejected'],
        ]);

        $this->workflowService->review(
            $dokumenProyek,
            (int) $validated['lock_version'],
            $validated['decision'],
            $validated['note'] ?? null
        );

        return redirect()->route('dokumen-proyek.show', $dokumenProyek)
            ->with('success', 'Hasil pemeriksaan Pengawas Lapangan berhasil disimpan.');
    }

    public function finalize(Request $request, DokumenProyek $dokumenProyek): RedirectResponse
    {
        $validated = $request->validate([
            'lock_version' => ['required', 'integer', 'min:0'],
            'decision' => ['required', Rule::in([
                DokumenVerificationStatus::Approved->value,
                DokumenVerificationStatus::RevisionRequired->value,
                DokumenVerificationStatus::Rejected->value,
            ])],
            'note' => ['nullable', 'string', 'max:2000', 'required_if:decision,revision_required,rejected'],
        ]);

        $this->workflowService->finalize(
            $dokumenProyek,
            (int) $validated['lock_version'],
            $validated['decision'],
            $validated['note'] ?? null
        );

        return redirect()->route('dokumen-proyek.show', $dokumenProyek)
            ->with('success', 'Keputusan akhir Direktur berhasil disimpan.');
    }
}
