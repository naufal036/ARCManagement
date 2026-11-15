<?php

namespace Database\Factories;

use App\Models\JenisPekerjaan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JenisPekerjaan>
 */
class JenisPekerjaanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_pekerjaan' => fake()->words(3, true),
            'deskripsi' => fake()->optional()->paragraph(),
        ];
    }
}
