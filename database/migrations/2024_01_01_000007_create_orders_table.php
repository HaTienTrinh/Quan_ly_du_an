<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Đặt hàng (Khách hàng) & Quản lý đơn hàng (Admin)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // VD: ORD-20240101-0001
            $table->foreignId('user_id')->constrained()->onDelete('restrict');

            // Thông tin giao hàng (snapshot tại thời điểm đặt)
            $table->string('receiver_name');
            $table->string('receiver_phone', 20);
            $table->string('receiver_province');
            $table->string('receiver_district');
            $table->string('receiver_ward');
            $table->string('receiver_address_detail');

            // Tiền
            $table->decimal('subtotal', 15, 2);       // Tổng tiền hàng
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);   // Thực thu

            // Trạng thái đơn hàng
            $table->enum('status', [
                'pending',      // Chờ xác nhận
                'confirmed',    // Đã xác nhận
                'processing',   // Đang chuẩn bị hàng
                'shipping',     // Đang giao
                'delivered',    // Đã giao
                'cancelled',    // Đã hủy
                'returned',     // Hoàn trả
            ])->default('pending');

            // Thanh toán
            $table->enum('payment_method', ['cod', 'bank_transfer', 'momo', 'vnpay'])->default('cod');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();

            $table->text('note')->nullable();              // Ghi chú của KH
            $table->text('admin_note')->nullable();        // Ghi chú nội bộ của Admin
            $table->foreignId('confirmed_by')->nullable()->constrained('users'); // Admin xác nhận
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
