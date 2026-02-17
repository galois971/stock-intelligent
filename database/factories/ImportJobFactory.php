<?php

namespace Database\Factories;

use App\Models\ImportJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImportJob>
 */
class ImportJobFactory extends Factory
{
    protected $model = ImportJob::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['product', 'stock_movement']),
            'status' => 'pending',
            'filename' => $this->faker->fileName('csv'),
            'total_rows' => $this->faker->numberBetween(10, 1000),
            'processed_rows' => 0,
            'failed_rows' => 0,
            'errors' => [],
        ];
    }
}
