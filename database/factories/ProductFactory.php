<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'barcode' => $this->faker->unique()->ean13(),
            'category_id' => null,
            'price' => $this->faker->randomFloat(2, 5, 500),
            'stock_min' => $this->faker->numberBetween(1, 20),
            'stock_optimal' => $this->faker->numberBetween(20, 100),
            'expiration_date' => $this->faker->optional(0.3)->dateTimeBetween('+7 days', '+180 days'),
        ];
    }
}
