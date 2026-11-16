<?php

namespace Database\Factories;

use App\Models\JenisPekerjaan;
use App\Models\Klien;
use App\Models\Proyek;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proyek>
 */
class ProyekFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_proyek' => fake()->unique()->bothify('PRJ-####??'),
            'nama_proyek' => fake()->sentence(4),
            'klien_id' => Klien::factory(),
            'jenis_pekerjaan_id' => JenisPekerjaan::factory(),
            'tanggal_mulai' => fake()->date(),
            'tanggal_selesai' => fake()->optional()->date(),
            'deskripsi' => fake()->optional()->paragraph(),
            'status' => 'Perencanaan',
        ];
    }
}
