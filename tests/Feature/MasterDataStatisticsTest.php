<?php

use App\Models\JenisPekerjaan;
use App\Models\Klien;
use App\Models\Proyek;
use App\Models\User;

it('shows database-backed statistics on the master data pages', function (): void {
    $user = User::factory()->create();
    $clientWithProjects = Klien::factory()->create();
    $clientWithAnotherProject = Klien::factory()->create();
    Klien::factory()->create();

    $usedWorkType = JenisPekerjaan::factory()->create();
    JenisPekerjaan::factory()->create();

    Proyek::factory()->create([
        'klien_id' => $clientWithProjects->id,
        'jenis_pekerjaan_id' => $usedWorkType->id,
        'status' => 'Perencanaan',
    ]);
    Proyek::factory()->create([
        'klien_id' => $clientWithProjects->id,
        'jenis_pekerjaan_id' => $usedWorkType->id,
        'status' => 'Proses',
    ]);
    Proyek::factory()->create([
        'klien_id' => $clientWithAnotherProject->id,
        'jenis_pekerjaan_id' => $usedWorkType->id,
        'status' => 'Selesai',
    ]);

    $this->actingAs($user)
        ->get(route('klien.index'))
        ->assertOk()
        ->assertViewHas('totalKlien', 3)
        ->assertViewHas('klienDenganProyek', 2)
        ->assertSeeInOrder(['Total Klien', '3', 'Klien dengan Proyek', '2']);

    $this->actingAs($user)
        ->get(route('jenis-pekerjaan.index'))
        ->assertOk()
        ->assertViewHas('totalJenisPekerjaan', 2)
        ->assertViewHas('jenisDenganProyek', 1)
        ->assertSeeInOrder(['Total Jenis Pekerjaan', '2', 'Digunakan pada Proyek', '1']);

    $this->actingAs($user)
        ->get(route('proyek.index'))
        ->assertOk()
        ->assertViewHas('totalProyek', 3)
        ->assertViewHas('proyekPerencanaan', 1)
        ->assertViewHas('proyekProses', 1)
        ->assertSeeInOrder(['Total Proyek', '3', 'Perencanaan', '1', 'Proses', '1']);
});
