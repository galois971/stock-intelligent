<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        $samples = [
            ['name' => 'Clavier mécanique', 'barcode' => 'KB-1001', 'price' => 59.90, 'stock_min' => 5, 'stock_optimal' => 30],
            ['name' => 'Souris optique', 'barcode' => 'MS-2002', 'price' => 19.50, 'stock_min' => 10, 'stock_optimal' => 50],
            ['name' => 'Papier A4 (500 feuilles)', 'barcode' => 'PA4-500', 'price' => 6.00, 'stock_min' => 20, 'stock_optimal' => 200],
            ['name' => 'Désinfectant 1L', 'barcode' => 'DS-1L', 'price' => 4.50, 'stock_min' => 10, 'stock_optimal' => 100],
            ['name' => 'Chargeur USB-C', 'barcode' => 'CH-USBC', 'price' => 14.99, 'stock_min' => 5, 'stock_optimal' => 40],
        ];

        foreach ($samples as $i => $s) {
            $category = $categories->get($i % $categories->count());
            Product::firstOrCreate(
                ['barcode' => $s['barcode']],
                array_merge($s, ['category_id' => $category->id])
            );
        }
    }
}
