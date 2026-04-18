<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductColorSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = ['38', '39', '40', '41', '42', '43'];

        $sampleProducts = [
            ['brand' => 'Nike',        'name' => 'Nike Air Force 1 Low',              'price' => 2490000, 'stock' => 50],
            ['brand' => 'Nike',        'name' => 'Nike Dunk Low',                     'price' => 2990000, 'stock' => 40],
            ['brand' => 'Adidas',      'name' => 'Adidas Samba OG',                   'price' => 2190000, 'stock' => 45],
            ['brand' => 'Adidas',      'name' => 'Adidas Stan Smith',                 'price' => 1890000, 'stock' => 60],
            ['brand' => 'Puma',        'name' => 'Puma Suede Classic',                'price' => 1390000, 'stock' => 35],
            ['brand' => 'New Balance', 'name' => 'New Balance 574 Core',              'price' => 1590000, 'stock' => 40],
            ['brand' => 'Converse',    'name' => 'Converse Chuck Taylor All Star High','price' => 1190000, 'stock' => 70],
        ];

        foreach ($sampleProducts as $data) {
            $category = Category::where('name', $data['brand'])->first();

            $product = Product::create([
                'category_id' => $category?->id ?? 1,
                'name'        => $data['name'],
                'slug'        => Str::slug($data['name']) . '-' . rand(100, 999),
                'price'       => $data['price'],
                'stock'       => $data['stock'],
                'is_active'   => true,
                'description' => "Giày {$data['name']} - thiết kế hiện đại, đế đệm êm ái, phù hợp cho cả thể thao lẫn đời thường.",
            ]);

            foreach ($sizes as $size) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'name'       => $size,
                ]);
            }

            $this->command->info("{$product->name}: " . count($sizes) . " size (38–43)");
        }
    }
}
