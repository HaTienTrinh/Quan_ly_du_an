<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();

            $table->enum('request_type', ['refund', 'exchange']);
            $table->enum('status', [
                'pending',
                'approved',
                'shipping_back',
                'received',
                'inspecting',
                'refunded',
                'exchanged',
                'completed',
                'rejected',
            ])->default('pending');

            $table->text('reason');
            $table->json('evidence_paths')->nullable();
            $table->enum('logistics_method', ['customer_ship', 'system_pickup'])->nullable();
            $table->text('admin_note')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('shipping_back_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('inspecting_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique('order_item_id');
            $table->index(['status', 'request_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
