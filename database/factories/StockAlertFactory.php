<?php

namespace Database\Factories;

use App\Models\StockAlert;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockAlert>
 */
class StockAlertFactory extends Factory
{
    protected $model = StockAlert::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $alertType = $this->faker->randomElement(['low_stock', 'overstock']);
        
        return [
            'product_id' => Product::factory(),
            'alert_type' => $alertType,
            'current_quantity' => $this->faker->numberBetween(1, 500),
            'message' => $this->faker->sentence(),
            'is_resolved' => false,
        ];
    }
}
