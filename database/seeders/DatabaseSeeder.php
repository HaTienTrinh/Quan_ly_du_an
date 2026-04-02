<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Post;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo Admin mặc định
        $admin = User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@example.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Tạo Khách hàng mẫu
        User::create([
            'name'      => 'Nguyễn Văn A',
            'email'     => 'customer@example.com',
            'password'  => Hash::make('password'),
            'role'      => 'customer',
            'phone'     => '0901234567',
            'is_active' => true,
        ]);

        // Tạo danh mục mẫu (Giày)
        $categories = [
            'Running - Giày chạy bộ',
            'Streetwear - Giày đường phố',
            'Classic - Giày cổ điển',
            'Basketball - Giày bóng rổ',
            'Casual - Giày thoải mái',
        ];
        foreach ($categories as $name) {
            Category::create([
                'name'      => $name,
                'slug'      => Str::slug($name),
                'is_active' => true,
            ]);
        }

        // Tạo 20 sản phẩm mẫu
        Product::factory()
            ->count(20)
            ->create();

        // Tạo 10 bài viết mẫu
        Post::factory()
            ->count(10)
            ->published()
            ->create([
                'author_id' => $admin->id,
            ]);

        // Tạo 5 bài viết draft
        Post::factory()
            ->count(5)
            ->draft()
            ->create([
                'author_id' => $admin->id,
            ]);
    }
}
