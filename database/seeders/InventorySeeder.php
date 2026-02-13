<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Product;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        if ($products->isEmpty()) {
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        foreach ($products as $product) {
            Inventory::firstOrCreate([
                'product_id' => $product->id,
                'inventory_date' => Carbon::now()->toDateString(),
            ], [
                'quantity' => rand(20, 150),
                'reference' => 'seed-' . now()->format('Ymd'),
            ]);
        }
    }
}
