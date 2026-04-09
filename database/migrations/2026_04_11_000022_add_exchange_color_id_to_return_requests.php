<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Thêm các cột vào return_requests:
//   - exchange_color_id: màu/size khách muốn đổi sang (chỉ dùng khi type=exchange)
//   - replacement_order_id đã có sẵn từ migration trước (2026_04_08_000014)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            // Biến thể (màu + size) khách muốn đổi sang
            // NULL = giữ nguyên biến thể cũ hoặc type=refund
            $table->foreignId('exchange_color_id')
                ->nullable()
                ->after('replacement_order_id')
                ->constrained('product_colors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('exchange_color_id');
        });
    }
};
