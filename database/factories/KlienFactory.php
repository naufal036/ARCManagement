<?php

namespace Database\Factories;

use App\Models\Klien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Klien>
 */
class KlienFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_klien' => fake()->company(),
            'alamat' => fake()->address(),
            'no_telepon' => fake()->numerify('08##########'),
            'email' => fake()->safeEmail(),
        ];
    }
}
