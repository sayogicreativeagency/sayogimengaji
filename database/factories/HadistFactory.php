<?php

namespace Database\Factories;

use App\Enums\Derajat;
use App\Enums\Kitab;
use App\Models\Hadist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hadist>
 */
class HadistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kitab' => Kitab::AbuDawud,
            'nomor' => (string) fake()->unique()->numberBetween(1, 99999),
            'nomor_arab' => null,
            'bab_nomor' => 1,
            'bab_judul' => 'Purification (Kitab Al-Taharah)',
            'teks_arab' => 'حدثنا',
            'terjemahan' => fake()->sentence(20),
            'derajat' => [],
            'derajat_ringkas' => Derajat::Shahih,
        ];
    }
}
