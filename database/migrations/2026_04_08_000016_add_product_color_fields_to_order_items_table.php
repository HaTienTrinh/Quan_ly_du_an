<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_color_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_colors')
                ->nullOnDelete();
            $table->string('product_color_name')->nullable()->after('product_name');
            $table->string('product_color_hex', 7)->nullable()->after('product_color_name');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_color_id');
            $table->dropColumn(['product_color_name', 'product_color_hex']);
        });
    }
};
