<?php

use App\Models\JenisPekerjaan;
use App\Models\Klien;
use App\Models\ProgressDokumentasi;
use App\Models\ProgressProyek;
use App\Models\ProjectAssignment;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('local');

    $this->user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $klien = Klien::create([
        'nama_klien' => 'PT Pengujian ARC',
        'alamat' => 'Jl. Pengujian No. 1',
        'no_telepon' => '021555000',
        'email' => 'pengujian@example.test',
    ]);

    $jenisPekerjaan = JenisPekerjaan::create([
        'nama_pekerjaan' => 'Pembangunan Gedung',
        'deskripsi' => 'Fixture untuk feature test.',
    ]);

    $this->proyek = Proyek::create([
        'kode_proyek' => 'TEST-001',
        'nama_proyek' => 'Proyek Pengujian Progress',
        'klien_id' => $klien->id,
        'jenis_pekerjaan_id' => $jenisPekerjaan->id,
        'tanggal_mulai' => '2026-01-01',
        'tanggal_selesai' => '2026-12-31',
        'deskripsi' => 'Fixture untuk feature test Progress Reporting.',
        'status' => 'Berjalan',
    ]);

    ProjectAssignment::create([
        'project_id' => $this->proyek->id,
        'user_id' => $this->user->id,
        'role_in_project' => 'Staf Teknis',
        'assigned_date' => '2026-01-01',
    ]);
});

function progressPayload(?int $projectId = null): array
{
    return [
        'proyek_id' => $projectId,
        'tanggal_laporan' => '2026-11-15',
        'persentase_progress' => '42.50',
        'uraian_pekerjaan' => 'Pekerjaan struktur lantai dua sedang berjalan.',
        'kendala' => 'Cuaca hujan.',
        'solusi' => 'Penyesuaian jadwal pekerjaan.',
        'catatan' => 'Perlu pemantauan lanjutan.',
        'rencana_selanjutnya' => 'Melanjutkan pekerjaan struktur.',
    ];
}

it('allows an authenticated user to view the progress index', function (): void {
    $progress = ProgressProyek::create(progressPayload($this->proyek->id) + [
        'user_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('progress-proyek.index'))
        ->assertOk()
        ->assertSee($progress->proyek->nama_proyek)
        ->assertSee('42.50');
});

it('redirects guests away from every progress page', function (): void {
    $progress = ProgressProyek::create(progressPayload($this->proyek->id));

    $this->get(route('progress-proyek.index'))->assertRedirect(route('login'));
    $this->get(route('progress-proyek.create'))->assertRedirect(route('login'));
    $this->get(route('progress-proyek.show', $progress))->assertRedirect(route('login'));
    $this->get(route('progress-proyek.edit', $progress))->assertRedirect(route('login'));
});

it('validates and stores a progress report for the authenticated user', function (): void {
    $payload = progressPayload($this->proyek->id);

    $this->actingAs($this->user)
        ->post(route('progress-proyek.store'), $payload)
        ->assertRedirect(route('progress-proyek.show', 1))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('progress_proyek', [
        'proyek_id' => $this->proyek->id,
        'user_id' => $this->user->id,
        'persentase_progress' => 42.50,
    ]);
});

it('rejects invalid progress input', function (): void {
    $this->actingAs($this->user)
        ->from(route('progress-proyek.create'))
        ->post(route('progress-proyek.store'), [
            'proyek_id' => 999999,
            'tanggal_laporan' => 'not-a-date',
            'persentase_progress' => 101,
            'uraian_pekerjaan' => '',
        ])
        ->assertRedirect(route('progress-proyek.create'))
        ->assertSessionHasErrors([
            'proyek_id',
            'tanggal_laporan',
            'persentase_progress',
            'uraian_pekerjaan',
        ]);
});

it('shows and updates a report without changing its owner or project', function (): void {
    $progress = ProgressProyek::create(progressPayload($this->proyek->id) + [
        'user_id' => $this->user->id,
    ]);
    $otherUser = User::factory()->create(['email_verified_at' => now()]);
    $otherProject = Proyek::create([
        'kode_proyek' => 'TEST-002',
        'nama_proyek' => 'Proyek Lain',
        'klien_id' => $this->proyek->klien_id,
        'jenis_pekerjaan_id' => $this->proyek->jenis_pekerjaan_id,
        'tanggal_mulai' => '2026-02-01',
        'status' => 'Perencanaan',
    ]);

    $this->actingAs($this->user)
        ->get(route('progress-proyek.show', $progress))
        ->assertOk()
        ->assertSee($progress->uraian_pekerjaan);

    $this->actingAs($this->user)
        ->put(route('progress-proyek.update', $progress), array_merge(progressPayload($otherProject->id), [
            'user_id' => $otherUser->id,
            'persentase_progress' => '75.00',
            'uraian_pekerjaan' => 'Uraian sudah diperbarui.',
        ]))
        ->assertRedirect(route('progress-proyek.show', $progress));

    $this->assertDatabaseHas('progress_proyek', [
        'id' => $progress->id,
        'proyek_id' => $this->proyek->id,
        'user_id' => $this->user->id,
        'persentase_progress' => 75.00,
        'uraian_pekerjaan' => 'Uraian sudah diperbarui.',
    ]);
});

it('accepts JPG, PNG, and PDF documentation uploads', function (): void {
    $progress = ProgressProyek::create(progressPayload($this->proyek->id) + [
        'user_id' => $this->user->id,
    ]);

    $files = [
        UploadedFile::fake()->image('foto.jpg'),
        UploadedFile::fake()->image('foto.png'),
        UploadedFile::fake()->create('laporan.pdf', 20, 'application/pdf'),
    ];

    foreach ($files as $file) {
        $this->actingAs($this->user)
            ->post(route('progress-proyek.dokumentasi.store', $progress), [
                'dokumentasi_file' => $file,
                'keterangan' => 'Dokumentasi pengujian.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    expect($progress->fresh()->dokumentasi)->toHaveCount(3);
    foreach ($progress->fresh()->dokumentasi as $documentation) {
        Storage::disk('local')->assertExists($documentation->path_file);
    }
});

it('rejects unsupported documentation files', function (): void {
    $progress = ProgressProyek::create(progressPayload($this->proyek->id) + [
        'user_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->from(route('progress-proyek.show', $progress))
        ->post(route('progress-proyek.dokumentasi.store', $progress), [
            'dokumentasi_file' => UploadedFile::fake()->create('malware.exe', 20, 'application/x-msdownload'),
        ])
        ->assertRedirect(route('progress-proyek.show', $progress))
        ->assertSessionHasErrors('dokumentasi_file');

    expect($progress->fresh()->dokumentasi)->toHaveCount(0);
});

it('can view and download documentation belonging to a report', function (): void {
    $progress = ProgressProyek::create(progressPayload($this->proyek->id) + [
        'user_id' => $this->user->id,
    ]);
    $this->actingAs($this->user)->post(route('progress-proyek.dokumentasi.store', $progress), [
        'dokumentasi_file' => UploadedFile::fake()->create('laporan.pdf', 20, 'application/pdf'),
    ]);
    $documentation = $progress->fresh()->dokumentasi->first();

    $this->actingAs($this->user)
        ->get(route('progress-proyek.dokumentasi.view', [$progress, $documentation]))
        ->assertOk();

    $this->actingAs($this->user)
        ->get(route('progress-proyek.dokumentasi.download', [$progress, $documentation]))
        ->assertDownload('laporan.pdf');
});

it('deletes documentation records and files when a report is deleted', function (): void {
    $progress = ProgressProyek::create(progressPayload($this->proyek->id) + [
        'user_id' => $this->user->id,
    ]);
    $this->actingAs($this->user)->post(route('progress-proyek.dokumentasi.store', $progress), [
        'dokumentasi_file' => UploadedFile::fake()->image('foto.jpg'),
    ]);
    $documentation = $progress->fresh()->dokumentasi->first();
    $path = $documentation->path_file;

    $this->actingAs($this->user)
        ->delete(route('progress-proyek.destroy', $progress))
        ->assertRedirect(route('progress-proyek.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('progress_proyek', ['id' => $progress->id]);
    $this->assertDatabaseMissing('progress_dokumentasi', ['id' => $documentation->id]);
    Storage::disk('local')->assertMissing($path);
});

it('renders the S-Curve with database progress and an explicit estimated baseline', function (): void {
    $progress = ProgressProyek::create(progressPayload($this->proyek->id) + [
        'user_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('progress-proyek.index', ['project_id' => $this->proyek->id]))
        ->assertOk()
        ->assertSee('Kurva S Progress')
        ->assertSee('Rencana kumulatif (baseline estimasi)')
        ->assertSee('Minggu 1')
        ->assertSee((string) $progress->persentase_progress);
});

it('filters the documentation report by project, date, and file name', function (): void {
    $progress = ProgressProyek::create(progressPayload($this->proyek->id) + [
        'user_id' => $this->user->id,
    ]);
    $path = 'progress-dokumentasi/'.$progress->id.'/foto-lapangan.jpg';
    Storage::disk('local')->put($path, 'fake-image');
    $documentation = ProgressDokumentasi::create([
        'progress_proyek_id' => $progress->id,
        'nama_file_asli' => 'foto-lapangan.jpg',
        'path_file' => $path,
        'keterangan' => 'Dokumentasi filter.',
    ]);

    $this->actingAs($this->user)
        ->get(route('laporan-dokumentasi.index', [
            'project_id' => $this->proyek->id,
            'date_from' => '2026-11-01',
            'date_to' => '2026-11-30',
            'search' => 'lapangan',
        ]))
        ->assertOk()
        ->assertSee($documentation->nama_file_asli)
        ->assertSee($progress->proyek->nama_proyek)
        ->assertSee('Dokumentasi filter.');
});

it('hides progress documentation from users without project access', function (): void {
    $outsider = User::factory()->create(['email_verified_at' => now()]);
    $progress = ProgressProyek::create(progressPayload($this->proyek->id) + [
        'user_id' => $this->user->id,
    ]);
    $path = 'progress-dokumentasi/'.$progress->id.'/private.pdf';
    Storage::disk('local')->put($path, '%PDF-1.4 private');
    $documentation = ProgressDokumentasi::create([
        'progress_proyek_id' => $progress->id,
        'nama_file_asli' => 'private.pdf',
        'path_file' => $path,
    ]);

    $this->actingAs($outsider)
        ->get(route('laporan-dokumentasi.index'))
        ->assertOk()
        ->assertDontSee('private.pdf')
        ->assertDontSee($this->proyek->nama_proyek);

    $this->actingAs($outsider)
        ->get(route('progress-proyek.dokumentasi.download', [$progress, $documentation]))
        ->assertForbidden();

    $this->actingAs($outsider)
        ->post(route('progress-proyek.dokumentasi.store', $progress), [
            'dokumentasi_file' => UploadedFile::fake()->create('blocked.pdf', 20, 'application/pdf'),
        ])
        ->assertForbidden();
});
