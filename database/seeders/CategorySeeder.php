<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Électronique'],
            ['name' => 'Fournitures de bureau'],
            ['name' => 'Entretien'],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate(['name' => $c['name']]);
        }
    }
}
