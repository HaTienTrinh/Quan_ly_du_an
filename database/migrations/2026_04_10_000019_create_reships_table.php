<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Bảng reships: lưu thông tin gửi lại hàng cho khách
// Dùng cho 2 trường hợp:
//   1. Hàng gian lận → gửi trả lại hàng cho khách
//   2. Yêu cầu exchange hợp lệ → gửi hàng đổi cho khách
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reships', function (Blueprint $table) {
            $table->id();

            // Liên kết với yêu cầu trả hàng
            $table->foreignId('return_id')
                ->constrained('return_requests')
                ->cascadeOnDelete();

            // Mã vận đơn (nhập sau khi gửi hàng)
            $table->string('tracking_code')->nullable();

            // Đơn vị vận chuyển (VD: GHN, GHTK, ViettelPost...)
            $table->string('carrier')->nullable();

            // Trạng thái giao hàng
            $table->enum('status', ['pending', 'shipped', 'delivered'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reships');
    }
};
