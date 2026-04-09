<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Bảng refunds: lưu thông tin hoàn tiền
// Chỉ tạo khi: hàng hợp lệ + type = refund
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();

            // Liên kết với yêu cầu trả hàng
            $table->foreignId('return_id')
                ->constrained('return_requests')
                ->cascadeOnDelete();

            // Số tiền hoàn lại
            $table->decimal('amount', 12, 2);

            // Trạng thái hoàn tiền
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');

            $table->timestamps();

            // Mỗi return_request chỉ có 1 refund
            $table->unique('return_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
