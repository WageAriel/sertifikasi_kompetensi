<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->words(3, true),
            'deskripsi_produk' => fake()->paragraph(),
            'harga' => fake()->randomFloat(2, 1000, 10000000),
            'stok' => fake()->numberBetween(1, 500),
        ];
    }
}
