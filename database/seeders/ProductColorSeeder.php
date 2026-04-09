<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Database\Seeder;

class ProductColorSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy category đầu tiên
        $category = \App\Models\Category::first();

        if (! $category) {
            $this->command->warn('Không có category. Hãy chạy DatabaseSeeder trước.');
            return;
        }

        // Dữ liệu sản phẩm mẫu kèm biến thể
        $sampleProducts = [
            [
                'name'  => 'Áo Thun Basic Unisex',
                'price' => 199000,
                'stock' => 50,
                'colors' => [
                    ['name' => 'Trắng', 'size' => 'S',  'hex_code' => '#FFFFFF'],
                    ['name' => 'Trắng', 'size' => 'M',  'hex_code' => '#FFFFFF'],
                    ['name' => 'Trắng', 'size' => 'L',  'hex_code' => '#FFFFFF'],
                    ['name' => 'Đen',   'size' => 'S',  'hex_code' => '#111111'],
                    ['name' => 'Đen',   'size' => 'M',  'hex_code' => '#111111'],
                    ['name' => 'Đen',   'size' => 'L',  'hex_code' => '#111111'],
                    ['name' => 'Đen',   'size' => 'XL', 'hex_code' => '#111111'],
                ],
            ],
            [
                'name'  => 'Quần Jeans Slim Fit',
                'price' => 450000,
                'stock' => 30,
                'colors' => [
                    ['name' => 'Xanh nhạt', 'size' => '28', 'hex_code' => '#93C5FD'],
                    ['name' => 'Xanh nhạt', 'size' => '29', 'hex_code' => '#93C5FD'],
                    ['name' => 'Xanh nhạt', 'size' => '30', 'hex_code' => '#93C5FD'],
                    ['name' => 'Xanh nhạt', 'size' => '31', 'hex_code' => '#93C5FD'],
                    ['name' => 'Xanh đậm',  'size' => '28', 'hex_code' => '#1E40AF'],
                    ['name' => 'Xanh đậm',  'size' => '29', 'hex_code' => '#1E40AF'],
                    ['name' => 'Xanh đậm',  'size' => '30', 'hex_code' => '#1E40AF'],
                    ['name' => 'Đen',       'size' => '29', 'hex_code' => '#111111'],
                    ['name' => 'Đen',       'size' => '30', 'hex_code' => '#111111'],
                    ['name' => 'Đen',       'size' => '31', 'hex_code' => '#111111'],
                ],
            ],
            [
                'name'  => 'Giày Sneaker Classic',
                'price' => 890000,
                'stock' => 20,
                'colors' => [
                    ['name' => 'Trắng/Đen', 'size' => '38', 'hex_code' => '#F3F4F6'],
                    ['name' => 'Trắng/Đen', 'size' => '39', 'hex_code' => '#F3F4F6'],
                    ['name' => 'Trắng/Đen', 'size' => '40', 'hex_code' => '#F3F4F6'],
                    ['name' => 'Trắng/Đen', 'size' => '41', 'hex_code' => '#F3F4F6'],
                    ['name' => 'Trắng/Đen', 'size' => '42', 'hex_code' => '#F3F4F6'],
                    ['name' => 'Đen toàn bộ', 'size' => '38', 'hex_code' => '#111111'],
                    ['name' => 'Đen toàn bộ', 'size' => '39', 'hex_code' => '#111111'],
                    ['name' => 'Đen toàn bộ', 'size' => '40', 'hex_code' => '#111111'],
                    ['name' => 'Đen toàn bộ', 'size' => '41', 'hex_code' => '#111111'],
                ],
            ],
            [
                'name'  => 'Áo Polo Nam',
                'price' => 320000,
                'stock' => 40,
                'colors' => [
                    ['name' => 'Xanh navy', 'size' => 'M',  'hex_code' => '#1E3A5F'],
                    ['name' => 'Xanh navy', 'size' => 'L',  'hex_code' => '#1E3A5F'],
                    ['name' => 'Xanh navy', 'size' => 'XL', 'hex_code' => '#1E3A5F'],
                    ['name' => 'Đỏ',       'size' => 'M',  'hex_code' => '#EF4444'],
                    ['name' => 'Đỏ',       'size' => 'L',  'hex_code' => '#EF4444'],
                    ['name' => 'Xám',      'size' => 'M',  'hex_code' => '#9CA3AF'],
                    ['name' => 'Xám',      'size' => 'L',  'hex_code' => '#9CA3AF'],
                    ['name' => 'Xám',      'size' => 'XL', 'hex_code' => '#9CA3AF'],
                ],
            ],
            [
                'name'  => 'Túi Tote Canvas',
                'price' => 150000,
                'stock' => 100,
                // Sản phẩm chỉ có màu, không có size
                'colors' => [
                    ['name' => 'Be',    'size' => null, 'hex_code' => '#D4B896'],
                    ['name' => 'Đen',  'size' => null, 'hex_code' => '#111111'],
                    ['name' => 'Xanh',  'size' => null, 'hex_code' => '#3B82F6'],
                    ['name' => 'Hồng', 'size' => null, 'hex_code' => '#EC4899'],
                ],
            ],
            [
                'name'  => 'Mũ Bucket Hat',
                'price' => 120000,
                'stock' => 60,
                // Sản phẩm không có biến thể (test trường hợp này)
                'colors' => [],
            ],
        ];

        foreach ($sampleProducts as $data) {
            $product = Product::create([
                'category_id' => $category->id,
                'name'        => $data['name'],
                'slug'        => \Illuminate\Support\Str::slug($data['name']) . '-' . rand(100, 999),
                'price'       => $data['price'],
                'stock'       => $data['stock'],
                'is_active'   => true,
                'description' => 'Sản phẩm mẫu để test biến thể màu và size.',
            ]);

            foreach ($data['colors'] as $variant) {
                ProductColor::create([
                    'product_id' => $product->id,
                    'name'       => $variant['name'],
                    'size'       => $variant['size'],
                    'hex_code'   => $variant['hex_code'],
                ]);
            }

            $count = count($data['colors']);
            $this->command->info("{$product->name}: " . ($count > 0 ? "{$count} biến thể" : 'không có biến thể'));
        }
    }
}
