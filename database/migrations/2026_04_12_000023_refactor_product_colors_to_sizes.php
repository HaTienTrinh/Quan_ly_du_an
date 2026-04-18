<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Refactor product_colors: sản phẩm chỉ có size, không có màu sắc
// - Bỏ cột hex_code
// - Cột name giữ nguyên (lưu tên size: S, M, L, 38, 39...)
// - Cột size (nếu có) được merge vào name rồi xóa
// - Đổi tên bảng thành product_sizes (alias, giữ FK cũ)
// - Đổi các cột color trong order_items thành size
return new class extends Migration
{
    public function up(): void
    {
        // 1. Nếu cột size tồn tại trong product_colors: copy size vào name rồi xóa
        if (Schema::hasColumn('product_colors', 'size')) {
            // Với các row có size, dùng size làm name
            DB::statement("UPDATE product_colors SET name = size WHERE size IS NOT NULL AND size != ''");
            Schema::table('product_colors', function (Blueprint $table) {
                $table->dropColumn('size');
            });
        }

        // 2. Bỏ hex_code
        if (Schema::hasColumn('product_colors', 'hex_code')) {
            Schema::table('product_colors', function (Blueprint $table) {
                $table->dropColumn('hex_code');
            });
        }

        // 3. Đổi tên cột trong order_items: product_color_name → product_size_name, product_color_hex → xóa
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_color_name')) {
                $table->renameColumn('product_color_name', 'product_size_name');
            }
            if (Schema::hasColumn('order_items', 'product_color_hex')) {
                $table->dropColumn('product_color_hex');
            }
        });

        // 4. Nếu product_size tồn tại trong order_items: copy vào product_size_name rồi xóa
        if (Schema::hasColumn('order_items', 'product_size')) {
            DB::statement("UPDATE order_items SET product_size_name = product_size WHERE product_size IS NOT NULL AND product_size != '' AND (product_size_name IS NULL OR product_size_name = '')");
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('product_size');
            });
        }
    }

    public function down(): void
    {
        Schema::table('product_colors', function (Blueprint $table) {
            $table->string('hex_code', 7)->nullable()->after('name');
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_size_name')) {
                $table->renameColumn('product_size_name', 'product_color_name');
            }
            $table->string('product_color_hex', 7)->nullable();
        });
    }
};
