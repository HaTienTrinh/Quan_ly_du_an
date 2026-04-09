<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration thêm cột violation_count vào bảng users
// Dùng để đếm số lần khách hàng bị phát hiện gian lận
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Số lần vi phạm (gian lận), mặc định = 0
            $table->unsignedInteger('violation_count')->default(0)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('violation_count');
        });
    }
};
