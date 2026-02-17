<?php

namespace Database\Factories;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMovement>
 */
class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['entry', 'exit']);
        
        $subtype = $type === 'entry' 
            ? $this->faker->randomElement(['achat', 'retour', 'correction'])
            : $this->faker->randomElement(['vente', 'perte', 'casse', 'expiration']);

        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'type' => $type,
            'subtype' => $subtype,
            'quantity' => $this->faker->numberBetween(1, 100),
            'motif' => $this->faker->sentence(),
            'movement_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'supplier' => $type === 'entry' ? $this->faker->company() : null,
            'customer' => $type === 'exit' ? $this->faker->name() : null,
        ];
    }
}
