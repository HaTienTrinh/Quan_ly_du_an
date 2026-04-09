<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Thêm cột size vào product_colors để mỗi biến thể có cả màu lẫn size
// VD: Đỏ - S, Đỏ - M, Xanh - L ...
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_colors', function (Blueprint $table) {
            // Size của biến thể: S, M, L, XL, 38, 39... (nullable = sản phẩm không có size)
            $table->string('size', 20)->nullable()->after('name');
        });

        // Thêm size snapshot vào order_items (lưu lại size tại thời điểm mua)
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_size', 20)->nullable()->after('product_color_hex');
        });
    }

    public function down(): void
    {
        Schema::table('product_colors', function (Blueprint $table) {
            $table->dropColumn('size');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_size');
        });
    }
};
