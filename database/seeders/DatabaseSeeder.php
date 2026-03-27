<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo Admin mặc định
        User::create([
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

        // Tạo danh mục mẫu
        $categories = ['Điện thoại', 'Laptop', 'Phụ kiện', 'Máy tính bảng'];
        foreach ($categories as $name) {
            Category::create([
                'name'      => $name,
                'slug'      => Str::slug($name),
                'is_active' => true,
            ]);
        }
    }
}
