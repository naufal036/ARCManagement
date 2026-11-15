<?php

namespace Database\Factories;

use App\Models\DokumenProyek;
use App\Models\KategoriDokumen;
use App\Models\Proyek;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DokumenProyek>
 */
class DokumenProyekFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proyek_id' => Proyek::factory(),
            'kategori_dokumen_id' => KategoriDokumen::factory(),
            'nama_dokumen' => fake()->sentence(3),
            'deskripsi' => fake()->optional()->paragraph(),
        ];
    }
}
