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
        return [
            'product_id' => Product::factory(), // génère un produit lié
            'alert_type' => $this->faker->randomElement([
                'low_stock',
                'overstock',
                'risk_of_rupture',
                'expiration'
            ]), // valeurs possibles
            'current_quantity' => $this->faker->numberBetween(1, 500), // quantité aléatoire
            'message' => $this->faker->sentence(), // message d’alerte
            'resolved' => false, // statut par défaut
        ];
    }
}