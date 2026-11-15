<?php

use App\Models\DokumenProyek;
use App\Models\KategoriDokumen;
use App\Models\Proyek;
use App\Models\User;
use App\Models\VersiDokumen;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function createDocumentManagementUser(): User
{
    $roleId = DB::table('roles')->insertGetId([
        'nama_role' => 'Admin',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::unguarded(fn (): User => User::factory()->create([
        'role_id' => $roleId,
    ]));
}

test('document and category pages require authentication', function () {
    $this->get(route('dokumen-proyek.index'))
        ->assertRedirect(route('login'));

    $this->get(route('kategori-dokumen.index'))
        ->assertRedirect(route('login'));
});

test('users can create document categories', function () {
    $user = createDocumentManagementUser();

    $response = $this->actingAs($user)->post(route('kategori-dokumen.store'), [
        'nama_kategori' => 'Gambar Teknik',
        'deskripsi' => 'Gambar kerja dan detail konstruksi.',
    ]);

    $response->assertRedirect(route('kategori-dokumen.index'));
    $this->assertDatabaseHas('kategori_dokumen', [
        'nama_kategori' => 'Gambar Teknik',
    ]);
});

test('users can update document categories', function () {
    $user = createDocumentManagementUser();

    $category = KategoriDokumen::factory()->create();

    $response = $this->actingAs($user)->put(route('kategori-dokumen.update', $category), [
        'nama_kategori' => 'Gambar Teknik',
        'deskripsi' => 'Gambar kerja dan detail konstruksi.',
    ]);

    $response->assertRedirect(route('kategori-dokumen.index'));
    $this->assertDatabaseHas('kategori_dokumen', [
        'id' => $category->id,
        'nama_kategori' => 'Gambar Teknik',
    ]);
});

test('category names must be unique', function () {
    $user = createDocumentManagementUser();
    KategoriDokumen::factory()->create(['nama_kategori' => 'Kontrak']);

    $response = $this->actingAs($user)
        ->from(route('kategori-dokumen.create'))
        ->post(route('kategori-dokumen.store'), [
            'nama_kategori' => 'Kontrak',
        ]);

    $response->assertSessionHasErrors('nama_kategori');
    $this->assertDatabaseCount('kategori_dokumen', 1);
});

test('categories used by documents cannot be deleted', function () {
    $user = createDocumentManagementUser();
    $document = DokumenProyek::factory()->create();

    $response = $this->actingAs($user)
        ->delete(route('kategori-dokumen.destroy', $document->kategoriDokumen));

    $response->assertRedirect(route('kategori-dokumen.index'));
    $response->assertSessionHas('status', 'kategori-dokumen-in-use');
    $this->assertModelExists($document->kategoriDokumen);
});

test('users can register project documents', function () {
    $user = createDocumentManagementUser();
    $project = Proyek::factory()->create();
    $category = KategoriDokumen::factory()->create();

    $response = $this->actingAs($user)->post(route('dokumen-proyek.store'), [
        'proyek_id' => $project->id,
        'kategori_dokumen_id' => $category->id,
        'nama_dokumen' => 'Gambar Struktur',
        'deskripsi' => 'Gambar struktur revisi awal.',
    ]);

    $document = DokumenProyek::query()->firstOrFail();
    $response->assertRedirect(route('dokumen-proyek.show', $document));
    $this->assertDatabaseHas('dokumen_proyek', [
        'proyek_id' => $project->id,
        'kategori_dokumen_id' => $category->id,
        'nama_dokumen' => 'Gambar Struktur',
    ]);
});

test('deleting a document also removes its stored versions', function () {
    Storage::fake('local');
    $user = createDocumentManagementUser();
    $document = DokumenProyek::factory()->create();
    $version = VersiDokumen::factory()->for($document, 'dokumenProyek')->create([
        'path_file' => 'dokumen-proyek/'.$document->id.'/versi-1.pdf',
    ]);
    Storage::disk('local')->put($version->path_file, 'private document contents');

    $response = $this->actingAs($user)
        ->delete(route('dokumen-proyek.destroy', $document));

    $response->assertRedirect(route('dokumen-proyek.index'));
    $this->assertDatabaseMissing('dokumen_proyek', ['id' => $document->id]);
    $this->assertDatabaseMissing('versi_dokumen', ['id' => $version->id]);
    Storage::disk('local')->assertMissing($version->path_file);
});

test('new uploads are stored privately as the next document version', function () {
    Storage::fake('local');
    $user = createDocumentManagementUser();
    $document = DokumenProyek::factory()->create();
    VersiDokumen::factory()->for($document, 'dokumenProyek')->create([
        'nomor_versi' => 1,
    ]);

    $response = $this->actingAs($user)->post(
        route('dokumen-proyek.versi.store', $document),
        [
            'file' => UploadedFile::fake()->create('gambar-revisi.pdf', 120, 'application/pdf'),
            'catatan' => 'Revisi pondasi.',
        ]
    );

    $version = VersiDokumen::query()
        ->where('dokumen_proyek_id', $document->id)
        ->where('nomor_versi', 2)
        ->firstOrFail();

    $response->assertRedirect(route('dokumen-proyek.show', $document));
    $this->assertDatabaseHas('versi_dokumen', [
        'id' => $version->id,
        'nomor_versi' => 2,
        'nama_file' => 'gambar-revisi.pdf',
        'catatan' => 'Revisi pondasi.',
        'user_id' => $user->id,
    ]);
    Storage::disk('local')->assertExists($version->path_file);
});

test('unsupported document files are rejected without creating a version', function () {
    $user = createDocumentManagementUser();
    $document = DokumenProyek::factory()->create();

    $response = $this->actingAs($user)
        ->from(route('dokumen-proyek.show', $document))
        ->post(route('dokumen-proyek.versi.store', $document), [
            'file' => UploadedFile::fake()->create('malware.exe', 10, 'application/x-msdownload'),
        ]);

    $response->assertSessionHasErrors('file');
    $this->assertDatabaseCount('versi_dokumen', 0);
});

test('stored document versions can only be downloaded by authenticated users', function () {
    Storage::fake('local');
    $version = VersiDokumen::factory()->create([
        'path_file' => 'dokumen-proyek/1/'.fake()->uuid().'.pdf',
        'nama_file' => 'gambar-kerja.pdf',
    ]);
    Storage::disk('local')->put($version->path_file, 'private document contents');

    $this->get(route('versi-dokumen.download', $version))
        ->assertRedirect(route('login'));

    $this->actingAs(createDocumentManagementUser())
        ->get(route('versi-dokumen.download', $version))
        ->assertDownload('gambar-kerja.pdf');
});

test('document names and descriptions are escaped in the details page', function () {
    $user = createDocumentManagementUser();
    $document = DokumenProyek::factory()->create([
        'nama_dokumen' => '<script>alert("x")</script>',
        'deskripsi' => '<img src=x onerror=alert(1)>',
    ]);

    $response = $this->actingAs($user)
        ->get(route('dokumen-proyek.show', $document));

    $response->assertOk()
        ->assertSee('&lt;script&gt;', false)
        ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false)
        ->assertDontSee('<script>alert("x")</script>', false);
});
