<?php

use App\Models\ActivityLog;
use App\Models\Contract;
use App\Models\ContractAddendum;
use App\Models\ProjectAssignment;
use App\Models\ProjectTask;
use App\Models\Proyek;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('local');
    $this->user = User::factory()->create();
    $this->proyek = Proyek::factory()->create();
});

function contractAssignmentPayload(?int $projectId = null, array $overrides = []): array
{
    return array_merge([
        'project_id' => $projectId,
        'contract_number' => 'KTR-TEST-001',
        'contract_name' => 'Kontrak Uji',
        'contract_date' => '2026-01-01',
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'contract_value' => '1000000.00',
        'status' => 'active',
    ], $overrides);
}

function assignmentPayload(int $projectId, int $userId, array $overrides = []): array
{
    return array_merge([
        'project_id' => $projectId,
        'user_id' => $userId,
        'role_in_project' => 'Ahli Struktur',
        'assigned_date' => '2026-01-01',
        'status' => 'active',
    ], $overrides);
}

function taskPayload(int $assignmentId, array $overrides = []): array
{
    return array_merge([
        'project_assignment_id' => $assignmentId,
        'task_name' => 'Pemeriksaan struktur',
        'task_description' => 'Pemeriksaan awal.',
        'start_date' => '2026-02-01',
        'due_date' => '2026-02-10',
        'priority' => 'medium',
        'status' => 'pending',
    ], $overrides);
}

function addendumPayload(array $overrides = []): array
{
    return array_merge([
        'addendum_number' => 'ADD-TEST-001',
        'addendum_date' => '2026-03-01',
        'value_after' => '1250000.00',
        'new_end_date' => '2026-11-30',
        'description' => 'Perubahan lingkup pekerjaan.',
    ], $overrides);
}

it('provides authenticated contract CRUD with project search and pagination', function (): void {
    $this->actingAs($this->user)
        ->post(route('contract.store'), contractAssignmentPayload($this->proyek->id))
        ->assertRedirect(route('contract.show', 1));

    $contract = Contract::firstOrFail();

    $this->assertDatabaseHas('contracts', [
        'id' => $contract->id,
        'project_id' => $this->proyek->id,
        'contract_number' => 'KTR-TEST-001',
    ]);

    $this->actingAs($this->user)
        ->get(route('contract.index', ['search' => 'KTR-TEST-001']))
        ->assertOk()
        ->assertSee($contract->contract_number);

    $this->actingAs($this->user)
        ->put(route('contract.update', $contract), contractAssignmentPayload($this->proyek->id, [
            'contract_name' => 'Kontrak Diperbarui',
        ]))
        ->assertRedirect(route('contract.show', $contract));

    $this->assertDatabaseHas('contracts', ['id' => $contract->id, 'contract_name' => 'Kontrak Diperbarui']);

    $this->actingAs($this->user)
        ->delete(route('contract.destroy', $contract))
        ->assertRedirect(route('contract.index'));

    $this->assertDatabaseMissing('contracts', ['id' => $contract->id]);
});

it('rejects invalid contract dates, values, statuses, and duplicate numbers', function (): void {
    $contract = Contract::create(contractAssignmentPayload($this->proyek->id));

    $this->actingAs($this->user)
        ->from(route('contract.create'))
        ->post(route('contract.store'), contractAssignmentPayload($this->proyek->id, [
            'contract_number' => $contract->contract_number,
            'end_date' => '2025-12-31',
            'contract_value' => '0',
            'status' => 'unknown',
        ]))
        ->assertRedirect(route('contract.create'))
        ->assertSessionHasErrors(['contract_number', 'end_date', 'contract_value', 'status']);
});

it('rejects contract and addendum values beyond the database decimal range', function (): void {
    $contract = Contract::create(contractAssignmentPayload($this->proyek->id));

    $this->actingAs($this->user)
        ->from(route('contract.create'))
        ->post(route('contract.store'), contractAssignmentPayload($this->proyek->id, [
            'contract_number' => 'KTR-TOO-LARGE',
            'contract_value' => '10000000000000.00',
        ]))
        ->assertRedirect(route('contract.create'))
        ->assertSessionHasErrors('contract_value');

    $this->actingAs($this->user)
        ->from(route('contract.show', $contract))
        ->post(route('contract.addendums.store', $contract), addendumPayload([
            'value_after' => '10000000000000.00',
        ]))
        ->assertRedirect(route('contract.show', $contract))
        ->assertSessionHasErrors('value_after');

    $this->assertDatabaseMissing('contracts', ['contract_number' => 'KTR-TOO-LARGE']);
    $this->assertDatabaseMissing('contract_addendums', ['contract_id' => $contract->id]);
});

it('rejects contract and addendum values with excess decimal places', function (): void {
    $contract = Contract::create(contractAssignmentPayload($this->proyek->id));

    $this->actingAs($this->user)
        ->from(route('contract.create'))
        ->post(route('contract.store'), contractAssignmentPayload($this->proyek->id, [
            'contract_number' => 'KTR-EXCESS-DECIMALS',
            'contract_value' => '100,001',
        ]))
        ->assertRedirect(route('contract.create'))
        ->assertSessionHasErrors('contract_value');

    $this->actingAs($this->user)
        ->from(route('contract.show', $contract))
        ->post(route('contract.addendums.store', $contract), addendumPayload([
            'value_after' => '100.001',
        ]))
        ->assertRedirect(route('contract.show', $contract))
        ->assertSessionHasErrors('value_after');
});

it('records activity using the application activity log schema', function (): void {
    $contract = Contract::create(contractAssignmentPayload($this->proyek->id));

    ActivityLog::record('Kontrak diperbarui', 'Data kontrak diperbarui.', $contract, $this->user->id);

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->user->id,
        'activity' => 'Kontrak diperbarui',
        'description' => 'Data kontrak diperbarui.',
        'subject_type' => 'Contract',
        'subject_id' => $contract->id,
    ]);
});

it('restricts user administration and settings to admins', function (): void {
    $adminRole = Role::create(['nama_role' => 'Admin']);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($this->user)->get(route('users.index'))->assertForbidden();
    $this->actingAs($this->user)->get(route('settings.index'))->assertForbidden();

    $this->actingAs($admin)->get(route('users.index'))->assertOk();
    $this->actingAs($admin)->get(route('settings.index'))->assertOk();
});

it('stores, downloads, replaces, and removes private contract files', function (): void {
    $this->actingAs($this->user)
        ->post(route('contract.store'), contractAssignmentPayload($this->proyek->id) + [
            'file_kontrak' => UploadedFile::fake()->create('kontrak-awal.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect(route('contract.show', 1));

    $contract = Contract::firstOrFail();
    $oldPath = $contract->file_path;

    Storage::disk('local')->assertExists($oldPath);
    $this->actingAs($this->user)
        ->get(route('contract.file.download', $contract))
        ->assertDownload('kontrak-awal.pdf');

    $this->actingAs($this->user)
        ->put(route('contract.update', $contract), contractAssignmentPayload($this->proyek->id, [
            'file_kontrak' => UploadedFile::fake()->create('kontrak-baru.pdf', 100, 'application/pdf'),
        ]))
        ->assertRedirect(route('contract.show', $contract));

    $contract->refresh();
    Storage::disk('local')->assertMissing($oldPath);
    Storage::disk('local')->assertExists($contract->file_path);
    expect($contract->file_name)->toBe('kontrak-baru.pdf');

    $currentPath = $contract->file_path;
    $this->actingAs($this->user)
        ->delete(route('contract.destroy', $contract))
        ->assertRedirect(route('contract.index'));

    Storage::disk('local')->assertMissing($currentPath);
});

it('creates and manages addenda without changing the original contract value', function (): void {
    $contract = Contract::create(contractAssignmentPayload($this->proyek->id));

    $this->actingAs($this->user)
        ->post(route('contract.addendums.store', $contract), addendumPayload())
        ->assertRedirect(route('contract.show', $contract));

    $addendum = ContractAddendum::firstOrFail();
    $this->assertDatabaseHas('contract_addendums', [
        'id' => $addendum->id,
        'contract_id' => $contract->id,
        'value_before' => 1000000,
        'value_after' => 1250000,
        'additional_value' => 250000,
    ]);
    $this->assertDatabaseHas('contracts', ['id' => $contract->id, 'contract_value' => 1000000]);

    $this->actingAs($this->user)
        ->get(route('contract.show', $contract))
        ->assertOk()
        ->assertSee('ADD-TEST-001')
        ->assertSee('Riwayat Addendum');

    $this->actingAs($this->user)
        ->put(route('contract.addendums.update', [$contract, $addendum]), addendumPayload([
            'value_after' => '1500000.00',
            'description' => 'Nilai disesuaikan.',
        ]))
        ->assertRedirect(route('contract.show', $contract));

    $this->assertDatabaseHas('contract_addendums', [
        'id' => $addendum->id,
        'value_before' => 1000000,
        'value_after' => 1500000,
    ]);

    $this->actingAs($this->user)
        ->delete(route('contract.addendums.destroy', [$contract, $addendum]))
        ->assertRedirect(route('contract.show', $contract));

    $this->assertDatabaseMissing('contract_addendums', ['id' => $addendum->id]);
});

it('validates addendum values and prevents deleting a contract that has addenda', function (): void {
    $contract = Contract::create(contractAssignmentPayload($this->proyek->id));

    $this->actingAs($this->user)
        ->from(route('contract.show', $contract))
        ->post(route('contract.addendums.store', $contract), addendumPayload([
            'value_after' => '-1',
            'new_end_date' => $contract->start_date->toDateString(),
        ]))
        ->assertRedirect(route('contract.show', $contract))
        ->assertSessionHasErrors(['value_after', 'new_end_date']);

    ContractAddendum::create([
        'contract_id' => $contract->id,
        'addendum_number' => 'ADD-LOCK',
        'addendum_date' => '2026-03-01',
        'additional_value' => 0,
    ]);

    $this->actingAs($this->user)
        ->delete(route('contract.destroy', $contract))
        ->assertRedirect(route('contract.show', $contract))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('contracts', ['id' => $contract->id]);
});

it('stores and downloads addendum PDFs and validates their file type', function (): void {
    $contract = Contract::create(contractAssignmentPayload($this->proyek->id));

    $this->actingAs($this->user)
        ->post(route('contract.addendums.store', $contract), addendumPayload() + [
            'file_addendum' => UploadedFile::fake()->create('addendum.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect(route('contract.show', $contract));

    $addendum = ContractAddendum::firstOrFail();
    Storage::disk('local')->assertExists($addendum->file_path);

    $this->actingAs($this->user)
        ->get(route('contract.addendums.file.download', [$contract, $addendum]))
        ->assertDownload('addendum.pdf');

    $oldPath = $addendum->file_path;
    $this->actingAs($this->user)
        ->put(route('contract.addendums.update', [$contract, $addendum]), addendumPayload([
            'file_addendum' => UploadedFile::fake()->create('addendum-baru.pdf', 100, 'application/pdf'),
        ]))
        ->assertRedirect(route('contract.show', $contract));

    $addendum->refresh();
    Storage::disk('local')->assertMissing($oldPath);
    Storage::disk('local')->assertExists($addendum->file_path);
    expect($addendum->file_name)->toBe('addendum-baru.pdf');

    $filePath = $addendum->file_path;
    $this->actingAs($this->user)
        ->delete(route('contract.addendums.destroy', [$contract, $addendum]))
        ->assertRedirect(route('contract.show', $contract));

    Storage::disk('local')->assertMissing($filePath);

    $this->actingAs($this->user)
        ->from(route('contract.show', $contract))
        ->post(route('contract.addendums.store', $contract), addendumPayload([
            'addendum_number' => 'ADD-BAD-FILE',
            'file_addendum' => UploadedFile::fake()->create('not-pdf.txt', 10, 'text/plain'),
        ]))
        ->assertRedirect(route('contract.show', $contract))
        ->assertSessionHasErrors('file_addendum');
});

it('rejects contract and addendum PDFs larger than 5 MB', function (): void {
    $this->actingAs($this->user)
        ->from(route('contract.create'))
        ->post(route('contract.store'), contractAssignmentPayload($this->proyek->id) + [
            'file_kontrak' => UploadedFile::fake()->create('terlalu-besar.pdf', 5121, 'application/pdf'),
        ])
        ->assertRedirect(route('contract.create'))
        ->assertSessionHasErrors('file_kontrak');

    $contract = Contract::create(contractAssignmentPayload($this->proyek->id));
    $this->actingAs($this->user)
        ->from(route('contract.show', $contract))
        ->post(route('contract.addendums.store', $contract), addendumPayload() + [
            'file_addendum' => UploadedFile::fake()->create('terlalu-besar.pdf', 5121, 'application/pdf'),
        ])
        ->assertRedirect(route('contract.show', $contract))
        ->assertSessionHasErrors('file_addendum');
});

it('allows repeat assignments only when earlier assignments are not active', function (): void {
    $payload = assignmentPayload($this->proyek->id, $this->user->id);

    $this->actingAs($this->user)
        ->post(route('project-assignment.store'), $payload)
        ->assertRedirect(route('project-assignment.show', 1));

    $this->actingAs($this->user)
        ->from(route('project-assignment.create'))
        ->post(route('project-assignment.store'), $payload)
        ->assertRedirect(route('project-assignment.create'))
        ->assertSessionHasErrors('user_id');

    $this->actingAs($this->user)
        ->post(route('project-assignment.store'), assignmentPayload($this->proyek->id, $this->user->id, [
            'status' => 'completed',
        ]))
        ->assertRedirect(route('project-assignment.show', 2));

    $this->actingAs($this->user)
        ->get(route('project-assignment.index', ['status' => 'completed']))
        ->assertOk()
        ->assertSee('Selesai');
});

it('supports project assignment updates and rejects invalid statuses', function (): void {
    $assignment = ProjectAssignment::create(assignmentPayload($this->proyek->id, $this->user->id));

    $this->actingAs($this->user)
        ->put(route('project-assignment.update', $assignment), assignmentPayload($this->proyek->id, $this->user->id, [
            'role_in_project' => 'Manajer Proyek',
            'status' => 'unknown',
        ]))
        ->assertSessionHasErrors('status');

    $this->actingAs($this->user)
        ->put(route('project-assignment.update', $assignment), assignmentPayload($this->proyek->id, $this->user->id, [
            'role_in_project' => 'Manajer Proyek',
        ]))
        ->assertRedirect(route('project-assignment.show', $assignment));

    $this->assertDatabaseHas('project_assignments', [
        'id' => $assignment->id,
        'role_in_project' => 'Manajer Proyek',
    ]);
});

it('creates, filters, updates, and deletes project tasks with date validation', function (): void {
    $assignment = ProjectAssignment::create(assignmentPayload($this->proyek->id, $this->user->id));

    $this->actingAs($this->user)
        ->from(route('project-task.create'))
        ->post(route('project-task.store'), taskPayload($assignment->id, [
            'due_date' => '2026-01-31',
        ]))
        ->assertRedirect(route('project-task.create'))
        ->assertSessionHasErrors('due_date');

    $this->actingAs($this->user)
        ->post(route('project-task.store'), taskPayload($assignment->id))
        ->assertRedirect(route('project-task.show', 1));

    $task = ProjectTask::firstOrFail();
    $this->actingAs($this->user)
        ->get(route('project-task.index', ['status' => 'pending', 'priority' => 'medium']))
        ->assertOk()
        ->assertSee($task->task_name);

    $this->actingAs($this->user)
        ->put(route('project-task.update', $task), taskPayload($assignment->id, [
            'status' => 'completed',
            'priority' => 'high',
        ]))
        ->assertRedirect(route('project-task.show', $task));

    $this->assertDatabaseHas('project_tasks', [
        'id' => $task->id,
        'status' => 'completed',
        'priority' => 'high',
    ]);

    $this->actingAs($this->user)
        ->delete(route('project-task.destroy', $task))
        ->assertRedirect(route('project-task.index'));
});

it('requires authentication for module routes and private downloads', function (): void {
    $contract = Contract::create(contractAssignmentPayload($this->proyek->id));
    $assignment = ProjectAssignment::create(assignmentPayload($this->proyek->id, $this->user->id));

    $this->get(route('contract.index'))->assertRedirect(route('login'));
    $this->get(route('project-assignment.index'))->assertRedirect(route('login'));
    $this->get(route('project-task.index'))->assertRedirect(route('login'));
    $this->get(route('contract.file.download', $contract))->assertRedirect(route('login'));
    $this->get(route('project-assignment.show', $assignment))->assertRedirect(route('login'));
});

it('renders create and edit forms across the module', function (): void {
    $contract = Contract::create(contractAssignmentPayload($this->proyek->id));
    $assignment = ProjectAssignment::create(assignmentPayload($this->proyek->id, $this->user->id));
    $task = ProjectTask::create(taskPayload($assignment->id));

    $this->actingAs($this->user)->get(route('contract.create'))->assertOk();
    $this->actingAs($this->user)
        ->get(route('contract.edit', $contract))
        ->assertOk()
        ->assertSee('value="2026-01-01"', false);
    $this->actingAs($this->user)->get(route('project-assignment.create'))->assertOk();
    $this->actingAs($this->user)->get(route('project-assignment.edit', $assignment))->assertOk();
    $this->actingAs($this->user)->get(route('project-task.create'))->assertOk();
    $this->actingAs($this->user)->get(route('project-task.edit', $task))->assertOk();
});
