<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');

            // Snapshot thông tin SP tại thời điểm đặt hàng
            $table->string('product_name');
            $table->string('product_thumbnail')->nullable();
            $table->decimal('unit_price', 15, 2);    // Giá tại lúc mua
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 15, 2);      // unit_price * quantity

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
