<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_order_list(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'name' => 'Nguyen Van Search',
            'role' => 'customer',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $order = Order::create([
            'order_code' => 'ORD-TEST-0001',
            'user_id' => $customer->id,
            'receiver_name' => 'Tran Thi Nhan',
            'receiver_phone' => '0900000000',
            'receiver_province' => 'HCM',
            'receiver_district' => 'Q1',
            'receiver_ward' => 'Ben Nghe',
            'receiver_address_detail' => '1 Test Street',
            'subtotal' => 100000,
            'shipping_fee' => 30000,
            'discount_amount' => 0,
            'total_amount' => 130000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_thumbnail' => $product->thumbnail,
            'unit_price' => 100000,
            'quantity' => 1,
            'subtotal' => 100000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.index'));

        $response->assertOk();
        $response->assertSee('ORD-TEST-0001');
        $response->assertSee('Nguyen Van Search');
    }

    public function test_admin_can_filter_orders_by_status_and_keyword(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'name' => 'Pham Van Match',
            'role' => 'customer',
        ]);

        Order::create([
            'order_code' => 'ORD-MATCH-0001',
            'user_id' => $customer->id,
            'receiver_name' => 'Pham Van Match',
            'receiver_phone' => '0900000001',
            'receiver_province' => 'HCM',
            'receiver_district' => 'Q1',
            'receiver_ward' => 'Ben Nghe',
            'receiver_address_detail' => '2 Match Street',
            'subtotal' => 100000,
            'shipping_fee' => 30000,
            'discount_amount' => 0,
            'total_amount' => 130000,
            'status' => Order::STATUS_SHIPPING,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
        ]);

        Order::create([
            'order_code' => 'ORD-OTHER-0002',
            'user_id' => $customer->id,
            'receiver_name' => 'Nguoi Khac',
            'receiver_phone' => '0900000002',
            'receiver_province' => 'HCM',
            'receiver_district' => 'Q3',
            'receiver_ward' => 'Ward 1',
            'receiver_address_detail' => '3 Other Street',
            'subtotal' => 200000,
            'shipping_fee' => 30000,
            'discount_amount' => 0,
            'total_amount' => 230000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.index', [
            'status' => Order::STATUS_SHIPPING,
            'q' => 'MATCH',
        ]));

        $response->assertOk();
        $response->assertSee('ORD-MATCH-0001');
        $response->assertDontSee('ORD-OTHER-0002');
    }
}
