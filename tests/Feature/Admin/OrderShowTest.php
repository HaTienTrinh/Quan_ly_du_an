<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_order_detail_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = Category::create([
            'name' => 'Danh mục thử nghiệm',
            'slug' => 'danh-muc-thu-nghiem',
            'is_active' => true,
        ]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Sản phẩm thử nghiệm',
        ]);

        $order = Order::create([
            'order_code' => 'ORD-SHOW-0001',
            'user_id' => $customer->id,
            'receiver_name' => 'Nguyễn Văn B',
            'receiver_phone' => '0909000001',
            'receiver_province' => 'Hồ Chí Minh',
            'receiver_district' => 'Quận 1',
            'receiver_ward' => 'Bến Nghé',
            'receiver_address_detail' => '12 Nguyễn Huệ',
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

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_thumbnail' => $product->thumbnail,
            'unit_price' => 500000,
            'quantity' => 1,
            'subtotal' => 500000,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'changed_by' => $admin->id,
            'from_status' => Order::STATUS_PENDING,
            'to_status' => Order::STATUS_CONFIRMED,
            'note' => 'Đã xác nhận đơn hàng.',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));

        $response->assertOk();
        $response->assertSee('ORD-SHOW-0001');
        $response->assertSee('Sản phẩm thử nghiệm');
        $response->assertSee('Nguyễn Văn B');
    }
}
