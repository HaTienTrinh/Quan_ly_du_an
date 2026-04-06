<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_confirm_pending_order(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $order = Order::create([
            'order_code' => 'ORD-CONFIRM-0001',
            'user_id' => $customer->id,
            'receiver_name' => 'Nguyễn Văn Xác Nhận',
            'receiver_phone' => '0909000001',
            'receiver_province' => 'Hồ Chí Minh',
            'receiver_district' => 'Quận 1',
            'receiver_ward' => 'Bến Nghé',
            'receiver_address_detail' => '1 Đường Test',
            'subtotal' => 300000,
            'shipping_fee' => 30000,
            'discount_amount' => 0,
            'total_amount' => 330000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.orders.confirm', $order), [
            'admin_note' => 'Đơn hàng hợp lệ, cho phép xử lý.',
        ]);

        $response->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CONFIRMED,
            'confirmed_by' => $admin->id,
            'admin_note' => 'Đơn hàng hợp lệ, cho phép xử lý.',
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'changed_by' => $admin->id,
            'from_status' => Order::STATUS_PENDING,
            'to_status' => Order::STATUS_CONFIRMED,
        ]);
    }

    public function test_admin_can_cancel_valid_order_with_reason(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $order = Order::create([
            'order_code' => 'ORD-CANCEL-0001',
            'user_id' => $customer->id,
            'receiver_name' => 'Nguyễn Văn Hủy',
            'receiver_phone' => '0909000002',
            'receiver_province' => 'Hồ Chí Minh',
            'receiver_district' => 'Quận 3',
            'receiver_ward' => 'Phường 1',
            'receiver_address_detail' => '2 Đường Test',
            'subtotal' => 400000,
            'shipping_fee' => 30000,
            'discount_amount' => 0,
            'total_amount' => 430000,
            'status' => Order::STATUS_CONFIRMED,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'confirmed_by' => $admin->id,
            'confirmed_at' => now(),
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.orders.cancel', $order), [
            'cancel_reason' => 'Thông tin nhận hàng không hợp lệ.',
            'admin_note' => 'Đã liên hệ nhưng không xác minh được đơn.',
        ]);

        $response->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CANCELLED,
            'cancel_reason' => 'Thông tin nhận hàng không hợp lệ.',
            'admin_note' => 'Đã liên hệ nhưng không xác minh được đơn.',
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'changed_by' => $admin->id,
            'from_status' => Order::STATUS_CONFIRMED,
            'to_status' => Order::STATUS_CANCELLED,
        ]);
    }

    public function test_admin_can_move_order_through_processing_shipping_and_delivered(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $order = Order::create([
            'order_code' => 'ORD-FLOW-0001',
            'user_id' => $customer->id,
            'receiver_name' => 'Nguyễn Văn Flow',
            'receiver_phone' => '0909000003',
            'receiver_province' => 'Hồ Chí Minh',
            'receiver_district' => 'Quận 7',
            'receiver_ward' => 'Tân Phú',
            'receiver_address_detail' => '3 Đường Test',
            'subtotal' => 500000,
            'shipping_fee' => 30000,
            'discount_amount' => 0,
            'total_amount' => 530000,
            'status' => Order::STATUS_CONFIRMED,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'confirmed_by' => $admin->id,
            'confirmed_at' => now(),
        ]);

        $this->actingAs($admin)->patch(route('admin.orders.prepare', $order))
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PROCESSING,
        ]);

        $this->actingAs($admin)->patch(route('admin.orders.ship', $order))
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_SHIPPING,
        ]);

        $this->actingAs($admin)->patch(route('admin.orders.complete', $order))
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_DELIVERED,
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'changed_by' => $admin->id,
            'from_status' => Order::STATUS_CONFIRMED,
            'to_status' => Order::STATUS_PROCESSING,
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'changed_by' => $admin->id,
            'from_status' => Order::STATUS_PROCESSING,
            'to_status' => Order::STATUS_SHIPPING,
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'changed_by' => $admin->id,
            'from_status' => Order::STATUS_SHIPPING,
            'to_status' => Order::STATUS_DELIVERED,
        ]);
    }
}
