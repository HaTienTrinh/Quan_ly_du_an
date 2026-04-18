<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $customer = User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '0901234567',
            'is_active' => true,
        ]);

        $categories = [
            'Nike',
            'Adidas',
            'Puma',
            'New Balance',
            'Converse',
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'is_active' => true,
            ]);
        }

        Product::factory()->count(20)->create();

        $sizes = ['38', '39', '40', '41', '42', '43'];
        Product::all()->each(function ($product) use ($sizes) {
            foreach ($sizes as $size) {
                \App\Models\ProductColor::create([
                    'product_id' => $product->id,
                    'name'       => 'Mặc định',
                    'size'       => $size,
                    'hex_code'   => null,
                ]);
            }
        });

        $featuredProductIds = Product::query()
            ->orderBy('id')
            ->take(6)
            ->pluck('id');

        Product::query()
            ->whereKey($featuredProductIds)
            ->update([
                'is_active' => true,
                'stock' => 30,
            ]);

        $products = Product::query()
            ->whereKey($featuredProductIds)
            ->get();

        Post::factory()->count(10)->published()->create([
            'author_id' => $admin->id,
        ]);

        Post::factory()->count(5)->draft()->create([
            'author_id' => $admin->id,
        ]);

        $sampleOrders = [
            [
                'order_code' => 'ORD-' . now()->subDays(4)->format('Ymd') . '-0001',
                'status' => Order::STATUS_PENDING,
                'payment_method' => 'cod',
                'payment_status' => 'unpaid',
                'receiver_name' => 'Nguyễn Văn A',
                'receiver_phone' => '0901234567',
                'created_at' => now()->subDays(4),
                'items' => [
                    ['index' => 0, 'quantity' => 1],['index' => 1, 'quantity' => 2],
                ],
            ],
            [
                'order_code' => 'ORD-' . now()->subDays(3)->format('Ymd') . '-0002',
                'status' => Order::STATUS_CONFIRMED,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid',
                'receiver_name' => 'Trần Thị B',
                'receiver_phone' => '0912345678',
                'created_at' => now()->subDays(3),
                'items' => [
                    ['index' => 2, 'quantity' => 1],
                ],
            ],
            [
                'order_code' => 'ORD-' . now()->subDays(2)->format('Ymd') . '-0003',
                'status' => Order::STATUS_SHIPPING,
                'payment_method' => 'vnpay',
                'payment_status' => 'paid',
                'receiver_name' => 'Lê Văn C',
                'receiver_phone' => '0923456789',
                'created_at' => now()->subDays(2),
                'items' => [
                    ['index' => 3, 'quantity' => 1],
                    ['index' => 4, 'quantity' => 1],
                ],
            ],
            [
                'order_code' => 'ORD-' . now()->subDay()->format('Ymd') . '-0004',
                'status' => Order::STATUS_DELIVERED,
                'payment_method' => 'momo',
                'payment_status' => 'paid',
                'receiver_name' => 'Phạm Thị D',
                'receiver_phone' => '0934567890',
                'created_at' => now()->subDay(),
                'items' => [
                    ['index' => 5, 'quantity' => 1],
                ],
            ],
        ];

        foreach ($sampleOrders as $sampleOrder) {
            $subtotal = 0;
            $preparedItems = [];

            foreach ($sampleOrder['items'] as $itemConfig) {
                $product = $products[$itemConfig['index'] % $products->count()];
                $unitPrice = (float) ($product->sale_price ?: $product->price);
                $quantity = $itemConfig['quantity'];
                $lineSubtotal = $unitPrice * $quantity;
                $subtotal += $lineSubtotal;

                $color = $product->colors()->where('size', '40')->first()
                    ?? $product->colors()->first();

                $preparedItems[] = [
                    'product'      => $product,
                    'color'        => $color,
                    'unit_price'   => $unitPrice,
                    'quantity'     => $quantity,
                    'subtotal'     => $lineSubtotal,
                ];
            }

            $isConfirmed = in_array($sampleOrder['status'], [
                Order::STATUS_CONFIRMED,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPING,
                Order::STATUS_DELIVERED,
            ], true);

            $order = Order::create([
                'order_code' => $sampleOrder['order_code'],'user_id' => $customer->id,
                'receiver_name' => $sampleOrder['receiver_name'],
                'receiver_phone' => $sampleOrder['receiver_phone'],
                'receiver_province' => 'Hồ Chí Minh',
                'receiver_district' => 'Quận 1',
                'receiver_ward' => 'Bến Nghé',
                'receiver_address_detail' => '123 Đường Mẫu',
                'subtotal' => $subtotal,
                'shipping_fee' => 30000,
                'discount_amount' => 0,
                'total_amount' => $subtotal + 30000,
                'status' => $sampleOrder['status'],
                'payment_method' => $sampleOrder['payment_method'],
                'payment_status' => $sampleOrder['payment_status'],
                'paid_at' => $sampleOrder['payment_status'] === 'paid' ? $sampleOrder['created_at'] : null,
                'note' => 'Đơn hàng mẫu để kiểm thử admin.',
                'confirmed_by' => $isConfirmed ? $admin->id : null,
                'confirmed_at' => $isConfirmed ? $sampleOrder['created_at'] : null,
                'created_at' => $sampleOrder['created_at'],
                'updated_at' => $sampleOrder['created_at'],
            ]);

            foreach ($preparedItems as $preparedItem) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $preparedItem['product']->id,
                    'product_color_id'   => $preparedItem['color']?->id,
                    'product_name'       => $preparedItem['product']->name,
                    'product_color_name' => $preparedItem['color']?->name,
                    'product_color_hex'  => $preparedItem['color']?->hex_code,
                    'product_size'       => $preparedItem['color']?->size,
                    'product_thumbnail'  => $preparedItem['product']->thumbnail,
                    'unit_price'         => $preparedItem['unit_price'],
                    'quantity'           => $preparedItem['quantity'],
                    'subtotal'           => $preparedItem['subtotal'],
                ]);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'changed_by' => $sampleOrder['status'] === Order::STATUS_PENDING ? $customer->id : $admin->id,
                'from_status' => null,
                'to_status' => $sampleOrder['status'],
                'note' => 'Khởi tạo dữ liệu đơn hàng mẫu.',
                'created_at' => $sampleOrder['created_at'],
                'updated_at' => $sampleOrder['created_at'],
            ]);
        }

        $reviewCustomers = collect([
            $customer,
            User::create([
                'name' => 'Minh Anh',
                'email' => 'minhanh@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '0945678901','is_active' => true,
            ]),
            User::create([
                'name' => 'Hai Nam',
                'email' => 'hainam@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '0956789012',
                'is_active' => true,
            ]),
            User::create([
                'name' => 'Linh Chi',
                'email' => 'linhchi@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '0967890123',
                'is_active' => true,
            ]),
        ]);

        $reviewProducts = Product::query()->take(4)->get();
        $reviewSamples = [
            [
                'rating' => 5,
                'comment' => 'Giày rất êm, đi cả ngày vẫn thoải mái và phối đồ rất đẹp.',
            ],
            [
                'rating' => 5,
                'comment' => 'Shop giao nhanh, đóng gói chỉn chu và tư vấn size khá chuẩn.',
            ],
            [
                'rating' => 4,
                'comment' => 'Form giày đẹp, chất liệu ổn, mang lên chân nhìn gọn và chắc.',
            ],
            [
                'rating' => 5,
                'comment' => 'Màu thực tế đẹp hơn ảnh, mang đi làm hay đi chơi đều hợp.',
            ],
        ];

        foreach ($reviewSamples as $index => $reviewSample) {
            $reviewedAt = now()->subDays(6 - $index);

            ProductReview::create([
                'user_id' => $reviewCustomers[$index]->id,
                'product_id' => $reviewProducts[$index]->id,
                'rating' => $reviewSample['rating'],
                'comment' => $reviewSample['comment'],
                'created_at' => $reviewedAt,
                'updated_at' => $reviewedAt,
            ]);
        }
    }
}