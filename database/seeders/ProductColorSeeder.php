<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductColorSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = ['38', '39', '40', '41', '42', '43'];

        $sampleProducts = [
            [
                'brand'  => 'Nike',
                'name'   => 'Nike Air Force 1 Low',
                'price'  => 2490000,
                'stock'  => 50,
                'colors' => [
                    ['name' => 'Trắng/Trắng', 'hex_code' => '#F8F8F8'],
                    ['name' => 'Đen/Đen',     'hex_code' => '#111111'],
                    ['name' => 'Trắng/Đen',   'hex_code' => '#E5E7EB'],
                ],
            ],
            [
                'brand'  => 'Nike',
                'name'   => 'Nike Dunk Low',
                'price'  => 2990000,
                'stock'  => 40,
                'colors' => [
                    ['name' => 'Pandas (Trắng/Đen)', 'hex_code' => '#F3F4F6'],
                    ['name' => 'University Red',     'hex_code' => '#DC2626'],
                    ['name' => 'Green Noise',        'hex_code' => '#4ADE80'],
                ],
            ],
            [
                'brand'  => 'Adidas',
                'name'   => 'Adidas Samba OG',
                'price'  => 2190000,
                'stock'  => 45,
                'colors' => [
                    ['name' => 'Core Black/White', 'hex_code' => '#1C1C1C'],
                    ['name' => 'Cloud White/Gum',  'hex_code' => '#FAFAFA'],
                    ['name' => 'Wonder Clay',      'hex_code' => '#C4A882'],
                ],
            ],
            [
                'brand'  => 'Adidas',
                'name'   => 'Adidas Stan Smith',
                'price'  => 1890000,
                'stock'  => 60,
                'colors' => [
                    ['name' => 'Cloud White/Green', 'hex_code' => '#F0FDF4'],
                    ['name' => 'Cloud White/Navy',  'hex_code' => '#EFF6FF'],
                ],
            ],
            [
                'brand'  => 'Puma',
                'name'   => 'Puma Suede Classic',
                'price'  => 1390000,
                'stock'  => 35,
                'colors' => [
                    ['name' => 'Puma Black',  'hex_code' => '#111111'],
                    ['name' => 'Puma Navy',   'hex_code' => '#1E3A5F'],
                    ['name' => 'Burnt Red',   'hex_code' => '#B91C1C'],
                ],
            ],
            [
                'brand'  => 'New Balance',
                'name'   => 'New Balance 574 Core',
                'price'  => 1590000,
                'stock'  => 40,
                'colors' => [
                    ['name' => 'Grey/White',  'hex_code' => '#9CA3AF'],
                    ['name' => 'Navy/White',  'hex_code' => '#1E40AF'],
                    ['name' => 'Black/White', 'hex_code' => '#1F2937'],
                ],
            ],
            [
                'brand'  => 'Converse',
                'name'   => 'Converse Chuck Taylor All Star High',
                'price'  => 1190000,
                'stock'  => 70,
                'colors' => [
                    ['name' => 'Optical White', 'hex_code' => '#F9FAFB'],
                    ['name' => 'Black',         'hex_code' => '#111111'],
                    ['name' => 'Navy',          'hex_code' => '#1E3A5F'],
                    ['name' => 'Red',           'hex_code' => '#EF4444'],
                ],
            ],
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

            foreach ($data['colors'] as $color) {
                foreach ($sizes as $size) {
                    ProductColor::create([
                        'product_id' => $product->id,
                        'name'       => $color['name'],
                        'size'       => $size,
                        'hex_code'   => $color['hex_code'],
                    ]);
                }
            }

            $variantCount = count($data['colors']) * count($sizes);
            $this->command->info("{$product->name}: {$variantCount} biến thể (size 38–43)");
        }
    }
}
