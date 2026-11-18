<?php

use App\Models\DokumenProyek;
use App\Models\JenisPekerjaan;
use App\Models\KategoriDokumen;
use App\Models\Klien;
use App\Models\Proyek;
use App\Models\ProjectAssignment;
use App\Models\Role;
use App\Models\User;
use App\Models\VersiDokumen;
use App\Services\DokumenFileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('local');

    $adminRole = Role::create(['nama_role' => 'Admin']);
    $staffRole = Role::create(['nama_role' => 'Staf Teknis']);

    $this->admin = User::factory()->create([
        'role_id' => $adminRole->id,
        'email_verified_at' => now(),
    ]);
    $this->staff = User::factory()->create([
        'role_id' => $staffRole->id,
        'email_verified_at' => now(),
    ]);
    $this->outsider = User::factory()->create([
        'role_id' => $staffRole->id,
        'email_verified_at' => now(),
    ]);

    $client = Klien::create(['nama_klien' => 'Klien DMS']);
    $jobType = JenisPekerjaan::create(['nama_pekerjaan' => 'Dokumen Teknik']);
    $this->project = Proyek::create([
        'kode_proyek' => 'DMS-001',
        'nama_proyek' => 'Proyek DMS',
        'klien_id' => $client->id,
        'jenis_pekerjaan_id' => $jobType->id,
        'tanggal_mulai' => '2026-01-01',
        'status' => 'Berjalan',
    ]);

    ProjectAssignment::create([
        'project_id' => $this->project->id,
        'user_id' => $this->staff->id,
        'role_in_project' => 'Staf Teknis',
        'assigned_date' => '2026-01-01',
    ]);

    $this->category = KategoriDokumen::create([
        'nama_kategori' => 'Gambar Teknik',
        'deskripsi' => 'Dokumen gambar proyek.',
    ]);
});

function dmsFakeFile(string $name, string $content, string $mime): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, $content)->mimeType($mime);
}

function createDmsDocument(array $attributes = []): DokumenProyek
{
    return DokumenProyek::create(array_merge([
        'proyek_id' => test()->project->id,
        'kategori_dokumen_id' => test()->category->id,
        'nama_dokumen' => 'Gambar Struktur',
        'deskripsi' => 'Dokumen pengujian DMS.',
    ], $attributes));
}

it('requires authentication for DMS pages', function (): void {
    $this->get(route('dokumen-proyek.index'))->assertRedirect(route('login'));
    $this->get(route('kategori-dokumen.index'))->assertRedirect(route('login'));
    $this->get(route('dokumen-proyek.create'))->assertRedirect(route('login'));
});

it('allows only admins and directors to manage document categories', function (): void {
    $this->actingAs($this->staff)->get(route('kategori-dokumen.index'))->assertForbidden();
    $this->actingAs($this->staff)->get(route('kategori-dokumen.create'))->assertForbidden();
    $this->actingAs($this->staff)->post(route('kategori-dokumen.store'), [
        'nama_kategori' => 'Terlarang',
    ])->assertForbidden();
    $this->actingAs($this->staff)->get(route('kategori-dokumen.edit', $this->category))->assertForbidden();
    $this->actingAs($this->staff)->put(route('kategori-dokumen.update', $this->category), [
        'nama_kategori' => 'Terlarang',
    ])->assertForbidden();
    $this->actingAs($this->staff)->delete(route('kategori-dokumen.destroy', $this->category))->assertForbidden();

    $directorRole = Role::create(['nama_role' => 'Direktur']);
    $director = User::factory()->create(['role_id' => $directorRole->id]);
    $this->actingAs($director)
        ->get(route('kategori-dokumen.index'))
        ->assertOk();
});

it('allows an authorized project member to view DMS and blocks outsiders', function (): void {
    $document = createDmsDocument();

    $this->actingAs($this->staff)
        ->get(route('dokumen-proyek.show', $document))
        ->assertOk()
        ->assertSee($document->nama_dokumen);

    $this->actingAs($this->outsider)
        ->get(route('dokumen-proyek.show', $document))
        ->assertForbidden();
});

it('blocks unauthorized document operations and target project changes', function (): void {
    $document = createDmsDocument();
    $otherProject = Proyek::create([
        'kode_proyek' => 'DMS-002',
        'nama_proyek' => 'Proyek Tanpa Assignment',
        'klien_id' => $this->project->klien_id,
        'jenis_pekerjaan_id' => $this->project->jenis_pekerjaan_id,
        'tanggal_mulai' => '2026-02-01',
        'status' => 'Perencanaan',
    ]);

    $this->actingAs($this->outsider)->get(route('dokumen-proyek.index'))->assertOk()->assertDontSee($document->nama_dokumen);
    $this->actingAs($this->outsider)->get(route('dokumen-proyek.edit', $document))->assertForbidden();
    $this->actingAs($this->outsider)->put(route('dokumen-proyek.update', $document), [
        'proyek_id' => $otherProject->id,
        'kategori_dokumen_id' => $this->category->id,
        'nama_dokumen' => 'Tidak boleh',
    ])->assertForbidden();
    $this->actingAs($this->outsider)->delete(route('dokumen-proyek.destroy', $document))->assertForbidden();
    $this->actingAs($this->outsider)->post(route('dokumen-proyek.versi.store', $document), [
        'files' => [dmsFakeFile('blocked.pdf', '%PDF-1.4 blocked', 'application/pdf')],
    ])->assertForbidden();

    $this->actingAs($this->staff)->put(route('dokumen-proyek.update', $document), [
        'proyek_id' => $otherProject->id,
        'kategori_dokumen_id' => $this->category->id,
        'nama_dokumen' => 'Percobaan pindah proyek',
    ])->assertForbidden();

    $this->assertDatabaseHas('dokumen_proyek', [
        'id' => $document->id,
        'proyek_id' => $this->project->id,
        'nama_dokumen' => 'Gambar Struktur',
    ]);
});

it('allows an assigned member to move a document only to another assigned project', function (): void {
    $document = createDmsDocument();
    $targetProject = Proyek::create([
        'kode_proyek' => 'DMS-003',
        'nama_proyek' => 'Proyek Target Assigned',
        'klien_id' => $this->project->klien_id,
        'jenis_pekerjaan_id' => $this->project->jenis_pekerjaan_id,
        'tanggal_mulai' => '2026-03-01',
        'status' => 'Berjalan',
    ]);
    ProjectAssignment::create([
        'project_id' => $targetProject->id,
        'user_id' => $this->staff->id,
        'role_in_project' => 'Staf Teknis',
        'assigned_date' => '2026-03-01',
    ]);

    $this->actingAs($this->staff)->put(route('dokumen-proyek.update', $document), [
        'proyek_id' => $targetProject->id,
        'kategori_dokumen_id' => $this->category->id,
        'nama_dokumen' => 'Dokumen Dipindahkan',
    ])->assertRedirect(route('dokumen-proyek.show', $document));

    $this->assertDatabaseHas('dokumen_proyek', [
        'id' => $document->id,
        'proyek_id' => $targetProject->id,
    ]);
});

it('allows admins to create and update categories and documents', function (): void {
    $this->actingAs($this->admin)
        ->post(route('kategori-dokumen.store'), [
            'nama_kategori' => 'Kontrak',
            'deskripsi' => 'Dokumen kontrak.',
        ])
        ->assertRedirect(route('kategori-dokumen.index'));

    $document = createDmsDocument();

    $this->actingAs($this->admin)
        ->put(route('dokumen-proyek.update', $document), [
            'proyek_id' => $this->project->id,
            'kategori_dokumen_id' => $this->category->id,
            'nama_dokumen' => 'Gambar Struktur Revisi',
            'deskripsi' => 'Metadata diperbarui.',
        ])
        ->assertRedirect(route('dokumen-proyek.show', $document));

    $this->assertDatabaseHas('dokumen_proyek', [
        'id' => $document->id,
        'nama_dokumen' => 'Gambar Struktur Revisi',
    ]);
});

it('uploads all supported document formats as sequential private versions', function (): void {
    $document = createDmsDocument();
    $files = [
        dmsFakeFile('dokumen.pdf', '%PDF-1.7 test', 'application/pdf'),
        UploadedFile::fake()->create('gambar.dwg', 10, 'application/acad'),
        UploadedFile::fake()->create('gambar.dxf', 10, 'application/dxf'),
        UploadedFile::fake()->create('surat.doc', 10, 'application/msword'),
        dmsFakeFile('surat.docx', 'PK test', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        UploadedFile::fake()->create('rencana.xls', 10, 'application/vnd.ms-excel'),
        dmsFakeFile('rencana.xlsx', 'PK test', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        UploadedFile::fake()->image('foto.png'),
        UploadedFile::fake()->image('foto.jpg'),
        UploadedFile::fake()->image('foto.jpeg'),
        dmsFakeFile('arsip.zip', 'PK test', 'application/zip'),
        dmsFakeFile('arsip.rar', 'Rar! test', 'application/vnd.rar'),
        UploadedFile::fake()->create('model.skp', 10, 'application/octet-stream'),
        UploadedFile::fake()->create('model.ifc', 10, 'application/octet-stream'),
    ];

    $this->actingAs($this->staff)
        ->from(route('dokumen-proyek.show', $document))
        ->post(route('dokumen-proyek.versi.store', $document), [
            'files' => $files,
            'catatan' => 'Upload batch pertama.',
        ])
        ->assertRedirect(route('dokumen-proyek.show', $document));

    $versions = $document->fresh()->versiDokumen;
    expect($versions)->toHaveCount(14);
    expect($versions->pluck('nomor_versi')->sort()->values()->all())->toBe(range(1, 14));

    foreach ($versions as $version) {
        Storage::disk('local')->assertExists($version->path_file);
        expect($version->path_file)->toStartWith('dokumen-proyek/'.$document->id.'/');
    }
});

it('rejects executable and unsupported extensions', function (): void {
    $document = createDmsDocument();

    $this->actingAs($this->staff)
        ->from(route('dokumen-proyek.show', $document))
        ->post(route('dokumen-proyek.versi.store', $document), [
            'files' => [UploadedFile::fake()->create('malware.exe', 10, 'application/x-msdownload')],
        ])
        ->assertRedirect(route('dokumen-proyek.show', $document))
        ->assertSessionHasErrors('files');

    $this->assertDatabaseCount('versi_dokumen', 0);
});

it('rejects MIME and magic-byte mismatches', function (): void {
    $document = createDmsDocument();

    $this->actingAs($this->staff)
        ->from(route('dokumen-proyek.show', $document))
        ->post(route('dokumen-proyek.versi.store', $document), [
            'files' => [dmsFakeFile('fake.pdf', '<html>not a pdf</html>', 'text/html')],
        ])
        ->assertRedirect(route('dokumen-proyek.show', $document))
        ->assertSessionHasErrors('files');

    $this->actingAs($this->staff)
        ->from(route('dokumen-proyek.show', $document))
        ->post(route('dokumen-proyek.versi.store', $document), [
            'files' => [dmsFakeFile('fake.pdf', '<html>not a pdf</html>', 'application/pdf')],
        ])
        ->assertRedirect(route('dokumen-proyek.show', $document))
        ->assertSessionHasErrors('files');

    $this->assertDatabaseCount('versi_dokumen', 0);
});

it('supports safe preview and download for authorized users', function (): void {
    $document = createDmsDocument();
    $path = 'dokumen-proyek/'.$document->id.'/'.fake()->uuid().'.pdf';
    Storage::disk('local')->put($path, '%PDF-1.4 test');
    $version = VersiDokumen::create([
        'dokumen_proyek_id' => $document->id,
        'nomor_versi' => 1,
        'nama_file_asli' => 'gambar-kerja.pdf',
        'nama_file_storage' => basename($path),
        'path_file' => $path,
        'disk' => 'local',
        'mime_type' => 'application/pdf',
        'ekstensi' => 'pdf',
        'ukuran_file' => 12,
        'checksum_sha256' => hash('sha256', '%PDF-1.4 test'),
        'user_id' => $this->staff->id,
    ]);

    $this->actingAs($this->staff)
        ->get(route('versi-dokumen.view', $version))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    $this->actingAs($this->staff)
        ->get(route('versi-dokumen.download', $version))
        ->assertDownload('gambar-kerja.pdf');

    $this->actingAs($this->outsider)
        ->get(route('versi-dokumen.download', $version))
        ->assertForbidden();
});

it('rejects versions stored on a non-private disk', function (): void {
    $document = createDmsDocument();
    $version = VersiDokumen::create([
        'dokumen_proyek_id' => $document->id,
        'nomor_versi' => 1,
        'nama_file_asli' => 'public.pdf',
        'nama_file_storage' => 'public.pdf',
        'path_file' => 'public.pdf',
        'disk' => 'public',
        'mime_type' => 'application/pdf',
        'ekstensi' => 'pdf',
        'ukuran_file' => 5,
        'checksum_sha256' => hash('sha256', 'unsafe'),
        'user_id' => $this->staff->id,
    ]);

    $this->actingAs($this->staff)
        ->get(route('versi-dokumen.download', $version))
        ->assertNotFound();
});

it('rejects changed and missing files before preview or download', function (): void {
    $document = createDmsDocument();
    $path = 'dokumen-proyek/'.$document->id.'/integrity.pdf';
    $original = '%PDF-1.4 integrity fixture';
    Storage::disk('local')->put($path, $original);
    $version = VersiDokumen::create([
        'dokumen_proyek_id' => $document->id,
        'nomor_versi' => 1,
        'nama_file_asli' => 'integrity.pdf',
        'nama_file_storage' => 'integrity.pdf',
        'path_file' => $path,
        'disk' => 'local',
        'mime_type' => 'application/pdf',
        'ekstensi' => 'pdf',
        'ukuran_file' => strlen($original),
        'checksum_sha256' => hash('sha256', $original),
        'user_id' => $this->staff->id,
    ]);

    Storage::disk('local')->put($path, '%PDF-1.4 tampered');
    $this->actingAs($this->staff)
        ->get(route('versi-dokumen.view', $version))
        ->assertStatus(409);

    Storage::disk('local')->delete($path);
    $this->actingAs($this->staff)
        ->get(route('versi-dokumen.download', $version))
        ->assertNotFound();
});

it('rejects batches above the file count and total size limits', function (): void {
    $document = createDmsDocument();

    $tooMany = array_map(
        fn (int $index): UploadedFile => UploadedFile::fake()->create("file-{$index}.dwg", 1, 'application/octet-stream'),
        range(1, 21)
    );
    $this->actingAs($this->staff)
        ->from(route('dokumen-proyek.show', $document))
        ->post(route('dokumen-proyek.versi.store', $document), ['files' => $tooMany])
        ->assertRedirect(route('dokumen-proyek.show', $document))
        ->assertSessionHasErrors('files');

    $tooLarge = array_map(
        fn (int $index): UploadedFile => UploadedFile::fake()->create("large-{$index}.dwg", 40 * 1024, 'application/octet-stream'),
        range(1, 3)
    );
    $this->actingAs($this->staff)
        ->from(route('dokumen-proyek.show', $document))
        ->post(route('dokumen-proyek.versi.store', $document), ['files' => $tooLarge])
        ->assertRedirect(route('dokumen-proyek.show', $document))
        ->assertSessionHasErrors('files');

    $this->assertDatabaseCount('versi_dokumen', 0);
});

it('retries storage cleanup and logs when the disk remains unavailable', function (): void {
    $disk = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
    $disk->shouldReceive('exists')->with('dokumen-proyek/1/orphan.pdf')->andReturn(true);
    $disk->shouldReceive('delete')->with(['dokumen-proyek/1/orphan.pdf'])->times(3)->andReturn(false);
    Storage::shouldReceive('disk')->with('local')->andReturn($disk);
    Log::spy();

    expect((new DokumenFileService)->cleanup('local', ['dokumen-proyek/1/orphan.pdf']))->toBeFalse();
    Log::shouldHaveReceived('error')->atLeast()->once();
});

it('reprocesses a tracked cleanup entry', function (): void {
    Storage::disk('local')->put('dokumen-proyek/1/orphan.pdf', 'orphan');
    Storage::disk('local')->put('dms-cleanup-pending.jsonl', json_encode([
        'disk' => 'local',
        'paths' => ['dokumen-proyek/1/orphan.pdf'],
    ]).PHP_EOL);
    $service = new DokumenFileService;

    expect($service->retryPendingCleanup(1))->toBe(1);
    Storage::disk('local')->assertMissing('dokumen-proyek/1/orphan.pdf');
    Storage::disk('local')->assertMissing('dms-cleanup-pending.jsonl');
});

it('rejects non-local disks and paths outside the DMS directory', function (): void {
    $service = new DokumenFileService;

    expect($service->cleanup('public', ['dokumen-proyek/1/file.pdf']))->toBeFalse();
    expect($service->cleanup('local', ['../outside.txt']))->toBeFalse();
    expect($service->cleanup('local', ['/dokumen-proyek/1/file.pdf']))->toBeFalse();
    expect($service->cleanup('local', ['dokumen-proyek/1/../outside.txt']))->toBeFalse();
    expect($service->cleanup('local', ['dokumen-proyek/1/..']))->toBeFalse();
    expect($service->cleanup('local', ['dokumen-proyek/1/.']))->toBeFalse();
});

it('preserves malformed and still-pending manifest entries', function (): void {
    Storage::disk('local')->put('dokumen-proyek/1/ready.pdf', 'ready');
    Storage::disk('local')->put('dms-cleanup-pending.jsonl', implode(PHP_EOL, [
        '{malformed json',
        json_encode(['disk' => 'local', 'paths' => ['dokumen-proyek/1/ready.pdf']]),
        json_encode(['disk' => 'local', 'paths' => ['../outside.pdf']]),
    ]).PHP_EOL);

    expect((new DokumenFileService)->retryPendingCleanup(1))->toBe(1);
    Storage::disk('local')->assertMissing('dokumen-proyek/1/ready.pdf');
    Storage::disk('local')->assertExists('dms-cleanup-pending.jsonl');
    expect(Storage::disk('local')->get('dms-cleanup-pending.jsonl'))
        ->toContain('{malformed json')
        ->toContain('outside.pdf');
});

it('preserves an entry appended while retry processing is in progress', function (): void {
    Storage::disk('local')->put('dokumen-proyek/1/first.pdf', 'first');
    Storage::disk('local')->put('dokumen-proyek/1/new.pdf', 'new');
    Storage::disk('local')->put('dms-cleanup-pending.jsonl', json_encode([
        'id' => 'first-entry',
        'disk' => 'local',
        'paths' => ['dokumen-proyek/1/first.pdf'],
    ]).PHP_EOL);

    $service = new class extends DokumenFileService
    {
        protected function deletePaths(string $diskName, array $paths, int $attempts): bool
        {
            $result = parent::deletePaths($diskName, $paths, $attempts);

            if ($paths === ['dokumen-proyek/1/first.pdf']) {
                Storage::disk('local')->append('dms-cleanup-pending.jsonl', json_encode([
                    'id' => 'new-entry',
                    'disk' => 'local',
                    'paths' => ['dokumen-proyek/1/new.pdf'],
                ]));
            }

            return $result;
        }
    };

    expect($service->retryPendingCleanup(1))->toBe(1);
    Storage::disk('local')->assertMissing('dokumen-proyek/1/first.pdf');
    Storage::disk('local')->assertExists('dokumen-proyek/1/new.pdf');
    expect(Storage::disk('local')->get('dms-cleanup-pending.jsonl'))->toContain('new-entry');
});

it('updates an existing multi-entry manifest without dropping pending entries', function (): void {
    Storage::disk('local')->put('dokumen-proyek/1/ready.pdf', 'ready');
    Storage::disk('local')->put('dms-cleanup-pending.jsonl', implode(PHP_EOL, [
        json_encode(['id' => 'ready-entry', 'disk' => 'local', 'paths' => ['dokumen-proyek/1/ready.pdf']]),
        json_encode(['id' => 'unsafe-entry', 'disk' => 'local', 'paths' => ['dokumen-proyek/1/..']]),
    ]).PHP_EOL);

    expect((new DokumenFileService)->retryPendingCleanup(1))->toBe(1);
    Storage::disk('local')->assertMissing('dokumen-proyek/1/ready.pdf');
    expect(Storage::disk('local')->get('dms-cleanup-pending.jsonl'))
        ->not->toContain('ready-entry')
        ->toContain('unsafe-entry');
});

it('releases the manifest lock when the protected callback fails', function (): void {
    $service = new DokumenFileService;
    $reflection = new ReflectionMethod($service, 'withManifestLock');
    $reflection->setAccessible(true);

    expect($reflection->invoke($service, function (): never {
        throw new RuntimeException('simulated manifest operation failure');
    }, 'fallback'))->toBe('fallback');

    $handle = fopen(Storage::disk('local')->path('dms-cleanup-pending.lock'), 'c');
    expect($handle)->toBeResource();
    expect(flock($handle, LOCK_EX | LOCK_NB))->toBeTrue();
    flock($handle, LOCK_UN);
    fclose($handle);
});

it('filters documents and prevents deleting a category in use', function (): void {
    $document = createDmsDocument();
    $path = 'dokumen-proyek/'.$document->id.'/filter.pdf';
    Storage::disk('local')->put($path, 'filter fixture');
    VersiDokumen::create([
        'dokumen_proyek_id' => $document->id,
        'nomor_versi' => 1,
        'nama_file_asli' => 'filter.pdf',
        'nama_file_storage' => 'filter.pdf',
        'path_file' => $path,
        'disk' => 'local',
        'mime_type' => 'application/pdf',
        'ekstensi' => 'pdf',
        'ukuran_file' => 14,
        'checksum_sha256' => hash('sha256', 'filter fixture'),
        'user_id' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
        ->get(route('dokumen-proyek.index', [
            'search' => 'Struktur',
            'proyek_id' => $this->project->id,
            'kategori_dokumen_id' => $this->category->id,
            'ekstensi' => 'pdf',
        ]))
        ->assertOk()
        ->assertSee($document->nama_dokumen);

    $this->actingAs($this->admin)
        ->delete(route('kategori-dokumen.destroy', $this->category))
        ->assertRedirect(route('kategori-dokumen.index'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('kategori_dokumen', ['id' => $this->category->id]);
});

it('deletes document metadata and all stored versions', function (): void {
    $document = createDmsDocument();
    $path = 'dokumen-proyek/'.$document->id.'/versi-1.pdf';
    Storage::disk('local')->put($path, 'private content');
    $version = VersiDokumen::create([
        'dokumen_proyek_id' => $document->id,
        'nomor_versi' => 1,
        'nama_file_asli' => 'versi-1.pdf',
        'nama_file_storage' => 'versi-1.pdf',
        'path_file' => $path,
        'disk' => 'local',
        'mime_type' => 'application/pdf',
        'ekstensi' => 'pdf',
        'ukuran_file' => 15,
        'checksum_sha256' => hash('sha256', 'private content'),
        'user_id' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
        ->delete(route('dokumen-proyek.destroy', $document))
        ->assertRedirect(route('dokumen-proyek.index'));

    $this->assertDatabaseMissing('dokumen_proyek', ['id' => $document->id]);
    $this->assertDatabaseMissing('versi_dokumen', ['id' => $version->id]);
    Storage::disk('local')->assertMissing($path);
});
