<?php

use App\Models\DokumenProyek;
use App\Models\JenisPekerjaan;
use App\Models\KategoriDokumen;
use App\Models\Klien;
use App\Models\Proyek;
use App\Models\Role;
use App\Models\User;
use App\Models\VersiDokumen;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('stores the expanded client profile', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = $this->actingAs($user)->post(route('klien.store'), [
        'nama_klien' => 'SMA Pengujian ARC',
        'jenis_client' => 'Sekolah',
        'npsn' => '12345678',
        'email' => 'sma@example.test',
    ]);

    $response->assertRedirect(route('klien.index'));
    $this->assertDatabaseHas('klien', [
        'nama_klien' => 'SMA Pengujian ARC',
        'jenis_client' => 'Sekolah',
        'npsn' => '12345678',
    ]);
});

it('deactivates a job type used by a project instead of deleting it', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $client = Klien::create(['nama_klien' => 'Client Riwayat', 'jenis_client' => 'Pemerintah']);
    $jobType = JenisPekerjaan::create(['nama_pekerjaan' => 'Pekerjaan Bersejarah']);
    $project = Proyek::create([
        'kode_proyek' => 'HIST-001',
        'nama_proyek' => 'Proyek Riwayat',
        'klien_id' => $client->id,
        'jenis_pekerjaan_id' => $jobType->id,
        'tanggal_mulai' => '2026-01-01',
        'status' => 'Selesai',
    ]);

    $response = $this->actingAs($user)->delete(route('jenis-pekerjaan.destroy', $jobType));

    $response->assertRedirect(route('jenis-pekerjaan.index'));
    $this->assertDatabaseHas('jenis_pekerjaan', ['id' => $jobType->id, 'is_active' => false]);
    $this->assertDatabaseHas('proyek', ['id' => $project->id, 'jenis_pekerjaan_id' => $jobType->id]);
});

it('stores and displays a project cover path', function (): void {
    Storage::fake('public');
    $user = User::factory()->create(['email_verified_at' => now()]);
    $client = Klien::create(['nama_klien' => 'Client Cover', 'jenis_client' => 'Swasta']);
    $jobType = JenisPekerjaan::create(['nama_pekerjaan' => 'Pekerjaan Cover']);

    $response = $this->actingAs($user)->post(route('proyek.store'), [
        'kode_proyek' => 'COVER-001',
        'nama_proyek' => 'Proyek Dengan Cover',
        'klien_id' => $client->id,
        'jenis_pekerjaan_id' => $jobType->id,
        'tanggal_mulai' => '2026-01-01',
        'status' => 'Perencanaan',
        'cover_image' => UploadedFile::fake()->image('cover.jpg'),
    ]);

    $response->assertRedirect(route('proyek.index'));
    $project = Proyek::query()->where('kode_proyek', 'COVER-001')->firstOrFail();

    expect($project->cover_image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($project->cover_image_path);
});

it('combines client and format filters on the document index', function (): void {
    $adminRole = Role::create(['nama_role' => 'Admin']);
    $user = User::factory()->create(['email_verified_at' => now(), 'role_id' => $adminRole->id]);
    $client = Klien::create(['nama_klien' => 'Client Filter', 'jenis_client' => 'Sekolah']);
    $jobType = JenisPekerjaan::create(['nama_pekerjaan' => 'Pekerjaan Filter']);
    $project = Proyek::create([
        'kode_proyek' => 'FILTER-001',
        'nama_proyek' => 'Proyek Filter',
        'klien_id' => $client->id,
        'jenis_pekerjaan_id' => $jobType->id,
        'tanggal_mulai' => '2026-01-01',
        'status' => 'Proses',
    ]);
    $category = KategoriDokumen::create(['nama_kategori' => 'BKU Test']);
    $document = DokumenProyek::create([
        'proyek_id' => $project->id,
        'kategori_dokumen_id' => $category->id,
        'nama_dokumen' => 'Laporan BKU PDF',
    ]);
    VersiDokumen::create([
        'dokumen_proyek_id' => $document->id,
        'nomor_versi' => 1,
        'nama_file_asli' => 'laporan.pdf',
        'nama_file_storage' => 'laporan.pdf',
        'path_file' => 'dokumen-proyek/1/laporan.pdf',
        'disk' => 'local',
        'mime_type' => 'application/pdf',
        'ekstensi' => 'pdf',
        'ukuran_file' => 100,
        'checksum_sha256' => str_repeat('a', 64),
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get(route('dokumen-proyek.index', [
        'client_id' => $client->id,
        'format' => 'pdf',
    ]));

    $response->assertOk()->assertSee('Laporan BKU PDF')->assertSee('Client Filter');
});
