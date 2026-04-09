<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Bảng inspections: lưu kết quả kiểm tra hàng trả về
// Mỗi return_request chỉ có 1 inspection (hasOne)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();

            // Liên kết với yêu cầu trả hàng
            $table->foreignId('return_id')
                ->constrained('return_requests')
                ->cascadeOnDelete();

            // is_valid = true  → hàng hợp lệ
            // is_valid = false → hàng gian lận
            $table->boolean('is_valid');

            // Ghi chú của admin khi kiểm tra
            $table->text('note')->nullable();

            $table->timestamps();

            // Mỗi return_request chỉ được kiểm tra 1 lần
            $table->unique('return_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
