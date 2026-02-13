<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Normalize keys (ensure lowercase headings)
        $name = $row['name'] ?? null;
        if (!$name) return null;

        $sku = $row['sku'] ?? null;
        $price = isset($row['price']) ? floatval($row['price']) : 0.0;
        $stock_min = isset($row['stock_min']) ? intval($row['stock_min']) : 0;
        $stock_optimal = isset($row['stock_optimal']) ? intval($row['stock_optimal']) : 0;
        $stock = isset($row['stock']) ? intval($row['stock']) : 0;

        // category: try id then name
        $categoryId = null;
        if (!empty($row['category_id'])) {
            $categoryId = intval($row['category_id']);
        } elseif (!empty($row['category']) || !empty($row['category_name'])) {
            $catName = $row['category'] ?? $row['category_name'];
            $category = Category::firstOrCreate(['name' => $catName]);
            $categoryId = $category->id;
        }

        $data = [
            'name' => $name,
            'sku' => $sku,
            'price' => $price,
            'stock_min' => $stock_min,
            'stock_optimal' => $stock_optimal,
            'stock' => $stock,
        ];

        if ($categoryId) $data['category_id'] = $categoryId;

        if ($sku) {
            return Product::updateOrCreate(['sku' => $sku], $data);
        }

        return Product::updateOrCreate(['name' => $name], $data);
    }
}
