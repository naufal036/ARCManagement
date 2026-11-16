<?php

namespace Database\Factories;

use App\Models\DokumenProyek;
use App\Models\VersiDokumen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VersiDokumen>
 */
class VersiDokumenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dokumen_proyek_id' => DokumenProyek::factory(),
            'nomor_versi' => 1,
            'nama_file' => fake()->word().'.pdf',
            'path_file' => fake()->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'ukuran_file' => fake()->numberBetween(1024, 1048576),
            'catatan' => fake()->optional()->sentence(),
            'user_id' => null,
        ];
    }
}
