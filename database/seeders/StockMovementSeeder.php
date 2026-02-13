<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockMovement;
use App\Models\Product;
use Carbon\Carbon;

class StockMovementSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        if ($products->isEmpty()) {
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        // Create historical movements for the last 60 days
        $start = Carbon::now()->subDays(60);
        foreach ($products as $product) {
            // random starting inventory entry
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'entry',
                'quantity' => rand(50, 200),
                'reference' => 'init',
                'movement_date' => $start->toDateTimeString(),
            ]);

            // daily random exits/entries
            for ($d = 0; $d < 60; $d++) {
                $date = $start->copy()->addDays($d);
                // a few days with entries
                if (rand(0, 6) === 0) {
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'entry',
                        'quantity' => rand(5, 30),
                        'movement_date' => $date->toDateTimeString(),
                    ]);
                }

                // regular exits
                if (rand(0, 1) === 1) {
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'exit',
                        'quantity' => rand(0, 10),
                        'movement_date' => $date->toDateTimeString(),
                    ]);
                }
            }
        }
    }
}
