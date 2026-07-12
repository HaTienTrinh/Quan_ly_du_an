<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductSize;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== USERS =====
        $admin = User::create([
            'name'       => 'Admin TT',
            'email'      => 'admin@ttshop.vn',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'is_active'  => true,
        ]);

        $customers = [
            User::create(['name' => 'Nguyễn Văn An',   'email' => 'an.nguyen@gmail.com',   'password' => Hash::make('password'), 'role' => 'customer', 'phone' => '0901234567', 'is_active' => true]),
            User::create(['name' => 'Trần Thị Bình',   'email' => 'binh.tran@gmail.com',   'password' => Hash::make('password'), 'role' => 'customer', 'phone' => '0912345678', 'is_active' => true]),
            User::create(['name' => 'Lê Minh Cường',   'email' => 'cuong.le@gmail.com',    'password' => Hash::make('password'), 'role' => 'customer', 'phone' => '0923456789', 'is_active' => true]),
            User::create(['name' => 'Phạm Thị Dung',   'email' => 'dung.pham@gmail.com',   'password' => Hash::make('password'), 'role' => 'customer', 'phone' => '0934567890', 'is_active' => true]),
            User::create(['name' => 'Hoàng Văn Em',    'email' => 'em.hoang@gmail.com',    'password' => Hash::make('password'), 'role' => 'customer', 'phone' => '0945678901', 'is_active' => true]),
        ];

        // ===== CATEGORIES =====
        $categoryData = [
            ['name' => 'Nike',        'description' => 'Giày thể thao Nike chính hãng - Just Do It'],
            ['name' => 'Adidas',      'description' => 'Giày Adidas - Impossible is Nothing'],
            ['name' => 'Puma',        'description' => 'Giày Puma - Forever Faster'],
            ['name' => 'New Balance', 'description' => 'Giày New Balance - Fearlessly Independent'],
            ['name' => 'Converse',    'description' => 'Giày Converse - Thiết kế cổ điển, phong cách bất biến'],
            ['name' => 'Vans',        'description' => 'Giày Vans - Off The Wall'],
        ];

        $categories = [];
        foreach ($categoryData as $cat) {
            $categories[] = Category::create([
                'name'        => $cat['name'],
                'slug'        => Str::slug($cat['name']),
                'description' => $cat['description'],
                'is_active'   => true,
            ]);
        }

        // ===== PRODUCTS =====
        $productData = [
            // Nike
            ['category' => 'Nike',        'name' => 'Nike Air Force 1 Low White',         'price' => 2490000, 'sale_price' => null,    'desc' => 'Đôi giày huyền thoại với thiết kế all-white tinh tế, đế Air đệm êm ái, phù hợp mọi phong cách.',
             'thumbnail' => 'https://i.pinimg.com/736x/8b/ae/98/8bae9814346f9e92d305c3d206f4248c.jpg'],
            ['category' => 'Nike',        'name' => 'Nike Dunk Low Panda',                'price' => 2990000, 'sale_price' => 2690000, 'desc' => 'Phối màu trắng đen cổ điển, form giày thấp cổ năng động, hot trend không bao giờ lỗi mốt.',
             'thumbnail' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600&q=80'],
            ['category' => 'Nike',        'name' => 'Nike Air Max 90',                    'price' => 3290000, 'sale_price' => null,    'desc' => 'Đệm Air Max lớn ở gót, thiết kế retro đặc trưng, thoải mái cho cả ngày dài.',
             'thumbnail' => 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=600&q=80'],
            ['category' => 'Nike',        'name' => 'Nike React Infinity Run',            'price' => 3590000, 'sale_price' => 3190000, 'desc' => 'Công nghệ React foam giảm chấn tối ưu, lý tưởng cho chạy bộ và tập luyện hàng ngày.',
             'thumbnail' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&q=80'],
            // Adidas
            ['category' => 'Adidas',      'name' => 'Adidas Samba OG Black White',        'price' => 2190000, 'sale_price' => null,    'desc' => 'Thiết kế sân cỏ cổ điển từ thập niên 70, chất da mềm mại, đế gum đặc trưng.',
             'thumbnail' => 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600&q=80'],
            ['category' => 'Adidas',      'name' => 'Adidas Stan Smith White Green',      'price' => 1890000, 'sale_price' => 1690000, 'desc' => 'Biểu tượng thời trang đường phố, thiết kế tối giản, dễ phối đồ mọi outfit.',
             'thumbnail' => 'https://images.unsplash.com/photo-1556906781-9a412961a28c?w=600&q=80'],
            ['category' => 'Adidas',      'name' => 'Adidas Ultraboost 22',               'price' => 3990000, 'sale_price' => null,    'desc' => 'Công nghệ Boost mang lại năng lượng hoàn hảo, lý tưởng cho runner chuyên nghiệp.',
             'thumbnail' => 'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=600&q=80'],
            ['category' => 'Adidas',      'name' => 'Adidas Gazelle Bold',                'price' => 2390000, 'sale_price' => 2190000, 'desc' => 'Phiên bản Gazelle đế dày trendy, màu sắc tươi sáng, hot item mùa này.',
             'thumbnail' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&q=80'],
            // Puma
            ['category' => 'Puma',        'name' => 'Puma Suede Classic Black',           'price' => 1390000, 'sale_price' => null,    'desc' => 'Chất da lộn mềm mại, thiết kế low-top đơn giản nhưng đẳng cấp.',
             'thumbnail' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80'],
            ['category' => 'Puma',        'name' => 'Puma RS-X Bold',                     'price' => 1890000, 'sale_price' => 1590000, 'desc' => 'Thiết kế chunky retro nổi bật, đế dày tạo chiều cao, phong cách Y2K.',
             'thumbnail' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&q=80'],
            // New Balance
            ['category' => 'New Balance', 'name' => 'New Balance 574 Grey',               'price' => 1590000, 'sale_price' => null,    'desc' => 'Giày chạy bộ cổ điển, đế ENCAP êm ái, màu xám trung tính dễ phối.',
             'thumbnail' => 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600&q=80'],
            ['category' => 'New Balance', 'name' => 'New Balance 990v6 Made in USA',      'price' => 5990000, 'sale_price' => null,    'desc' => 'Sản xuất tại Mỹ, chất lượng premium, biểu tượng của New Balance.',
             'thumbnail' => 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=600&q=80'],
            ['category' => 'New Balance', 'name' => 'New Balance 530 White Silver',       'price' => 1990000, 'sale_price' => 1790000, 'desc' => 'Phối màu trắng bạc sáng bóng, đế chunky thời thượng, hot trend hiện tại.',
             'thumbnail' => 'https://images.unsplash.com/photo-1556906781-9a412961a28c?w=600&q=80'],
            // Converse
            ['category' => 'Converse',    'name' => 'Converse Chuck Taylor All Star High White', 'price' => 1190000, 'sale_price' => null,    'desc' => 'Cổ cao huyền thoại, canvas trắng tinh khôi, biểu tượng văn hóa đường phố.',
             'thumbnail' => 'https://images.unsplash.com/photo-1463100099107-aa0980c362e6?w=600&q=80'],
            ['category' => 'Converse',    'name' => 'Converse Chuck 70 Vintage Canvas',   'price' => 1490000, 'sale_price' => 1290000, 'desc' => 'Phiên bản vintage với chất liệu canvas dày dặn, logo thêu tay tinh tế.',
             'thumbnail' => 'https://images.unsplash.com/photo-1494496195158-c3bc975be088?w=600&q=80'],
            // Vans
            ['category' => 'Vans',        'name' => 'Vans Old Skool Black White',         'price' => 1290000, 'sale_price' => null,    'desc' => 'Sọc Jazz đặc trưng, đế waffle chống trượt, thiết kế skate cổ điển bất hủ.',
             'thumbnail' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600&q=80'],
            ['category' => 'Vans',        'name' => 'Vans Sk8-Hi Black',                  'price' => 1490000, 'sale_price' => 1390000, 'desc' => 'Cổ cao bảo vệ mắt cá, chất canvas bền bỉ, phong cách skate đường phố.',
             'thumbnail' => 'https://images.unsplash.com/photo-1512374382149-233c42b6a83b?w=600&q=80'],
            ['category' => 'Vans',        'name' => 'Vans Authentic Lo Pro White',        'price' => 990000,  'sale_price' => null,    'desc' => 'Thiết kế tối giản nhất của Vans, nhẹ nhàng thoải mái, dễ phối mọi outfit.',
             'thumbnail' => 'https://images.unsplash.com/photo-1465453869711-7e174808ace9?w=600&q=80'],
        ];

        $sizes = ['36', '37', '38', '39', '40', '41', '42', '43'];
        $categoryMap = Category::pluck('id', 'name');
        $products = [];

        foreach ($productData as $pd) {
            $product = Product::create([
                'category_id' => $categoryMap[$pd['category']],
                'name'        => $pd['name'],
                'slug'        => Str::slug($pd['name']) . '-' . rand(100, 999),
                'description' => $pd['desc'],
                'price'       => $pd['price'],
                'sale_price'  => $pd['sale_price'],
                'stock'       => 0,
                'thumbnail'   => $pd['thumbnail'],
                'is_active'   => true,
            ]);

            $totalStock = 0;
            foreach ($sizes as $size) {
                $stock = rand(5, 20);
                ProductSize::create([
                    'product_id' => $product->id,
                    'name'       => $size,
                    'stock'      => $stock,
                ]);
                $totalStock += $stock;
            }

            $product->update(['stock' => $totalStock]);
            $products[] = $product;
        }

        // ===== POSTS =====
        $postData = [
            ['title' => 'Top 5 đôi giày Nike hot nhất 2025',                    'summary' => 'Điểm qua những mẫu giày Nike đang làm mưa làm gió trên thị trường năm nay.'],
            ['title' => 'Adidas Samba - Từ sân cỏ đến đường phố',               'summary' => 'Hành trình của đôi giày huyền thoại từ sân bóng đến trở thành biểu tượng thời trang.'],
            ['title' => 'Hướng dẫn chọn size giày chuẩn nhất',                  'summary' => 'Bí quyết chọn đúng size giày để tránh mua nhầm và đảm bảo thoải mái khi đi.'],
            ['title' => 'Cách vệ sinh giày trắng sạch như mới',                 'summary' => 'Những mẹo đơn giản giúp đôi giày trắng của bạn luôn sáng bóng như ngày đầu.'],
            ['title' => 'Vans Old Skool - Biểu tượng văn hóa skate',            'summary' => 'Tìm hiểu lịch sử và sức hút bền vững của đôi giày gắn liền với văn hóa skateboard.'],
            ['title' => 'New Balance 990 - Vì sao đắt mà vẫn hot?',             'summary' => 'Giải mã sức hút của dòng giày Made in USA đắt nhất trong lineup New Balance.'],
            ['title' => 'Xu hướng giày chunky sole 2025',                       'summary' => 'Đế dày đang trở lại mạnh mẽ - những mẫu giày bạn không thể bỏ qua mùa này.'],
            ['title' => 'Phối đồ với giày Converse: 10 cách không bao giờ lỗi', 'summary' => 'Từ casual đến smart casual, Converse luôn là lựa chọn hoàn hảo cho mọi outfit.'],
            ['title' => 'Puma Suede - 50 năm vẫn không lỗi mốt',                'summary' => 'Câu chuyện về đôi giày da lộn đã đồng hành cùng nhiều thế hệ yêu thời trang.'],
            ['title' => 'Bảo quản giày da đúng cách để dùng bền lâu',           'summary' => 'Những lưu ý quan trọng khi bảo quản giày da giúp giày luôn đẹp và bền theo thời gian.'],
        ];

        $postThumbnails = [
            'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80',
            'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600&q=80',
            'https://images.unsplash.com/photo-1556906781-9a412961a28c?w=600&q=80',
            'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600&q=80',
            'https://images.unsplash.com/photo-1465453869711-7e174808ace9?w=600&q=80',
            'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=600&q=80',
            'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600&q=80',
            'https://images.unsplash.com/photo-1463100099107-aa0980c362e6?w=600&q=80',
            'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=600&q=80',
            'https://images.unsplash.com/photo-1512374382149-233c42b6a83b?w=600&q=80',
        ];

        foreach ($postData as $i => $pd) {
            Post::create([
                'author_id'    => $admin->id,
                'title'        => $pd['title'],
                'slug'         => Str::slug($pd['title']),
                'summary'      => $pd['summary'],
                'content'      => $this->generatePostContent($pd['title']),
                'thumbnail'    => $postThumbnails[$i] ?? null,
                'status'       => $i < 8 ? 'published' : 'draft',
                'published_at' => $i < 8 ? now()->subDays(rand(1, 30)) : null,
            ]);
        }

        // ===== ORDERS =====
        $orderSamples = [
            ['customer' => 0, 'status' => 'pending',   'payment' => 'cod',          'paid' => false,  'days' => 1],
            ['customer' => 1, 'status' => 'confirmed',  'payment' => 'cod',          'paid' => false,  'days' => 3],
            ['customer' => 2, 'status' => 'processing', 'payment' => 'vnpay',        'paid' => true,   'days' => 5],
            ['customer' => 3, 'status' => 'shipping',   'payment' => 'vnpay',        'paid' => true,   'days' => 7],
            ['customer' => 4, 'status' => 'delivered',  'payment' => 'cod',          'paid' => true,   'days' => 10],
            ['customer' => 0, 'status' => 'delivered',  'payment' => 'bank_transfer','paid' => true,   'days' => 15],
            ['customer' => 1, 'status' => 'cancelled',  'payment' => 'cod',          'paid' => false,  'days' => 8],
        ];

        foreach ($orderSamples as $idx => $os) {
            $customer  = $customers[$os['customer']];
            $createdAt = now()->subDays($os['days']);

            // Chọn 1-2 sản phẩm ngẫu nhiên
            $selectedProducts = collect($products)->random(rand(1, 2));
            $subtotal = 0;
            $items = [];

            foreach ($selectedProducts as $product) {
                $size      = $product->sizes()->inRandomOrder()->first();
                $unitPrice = (float) ($product->sale_price ?: $product->price);
                $qty       = rand(1, 2);
                $sub       = $unitPrice * $qty;
                $subtotal += $sub;
                $items[]   = compact('product', 'size', 'unitPrice', 'qty', 'sub');
            }

            $order = Order::create([
                'order_code'              => 'ORD-' . $createdAt->format('Ymd') . '-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                'user_id'                 => $customer->id,
                'receiver_name'           => $customer->name,
                'receiver_phone'          => $customer->phone,
                'receiver_province'       => 'Hà Nội',
                'receiver_district'       => 'Cầu Giấy',
                'receiver_ward'           => 'Dịch Vọng',
                'receiver_address_detail' => rand(10, 200) . ' Đường Xuân Thủy',
                'subtotal'                => $subtotal,
                'shipping_fee'            => 30000,
                'discount_amount'         => 0,
                'total_amount'            => $subtotal + 30000,
                'status'                  => $os['status'],
                'payment_method'          => $os['payment'],
                'payment_status'          => $os['paid'] ? 'paid' : 'unpaid',
                'paid_at'                 => $os['paid'] ? $createdAt : null,
                'confirmed_by'            => in_array($os['status'], ['confirmed','processing','shipping','delivered']) ? $admin->id : null,
                'confirmed_at'            => in_array($os['status'], ['confirmed','processing','shipping','delivered']) ? $createdAt : null,
                'created_at'              => $createdAt,
                'updated_at'              => $createdAt,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'          => $order->id,
                    'product_id'        => $item['product']->id,
                    'product_color_id'  => $item['size']?->id,
                    'product_name'      => $item['product']->name,
                    'product_size_name' => $item['size']?->name,
                    'product_thumbnail' => $item['product']->thumbnail,
                    'unit_price'        => $item['unitPrice'],
                    'quantity'          => $item['qty'],
                    'subtotal'          => $item['sub'],
                ]);
            }

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'changed_by'  => $customer->id,
                'from_status' => null,
                'to_status'   => $os['status'],
                'note'        => 'Đơn hàng được tạo.',
                'created_at'  => $createdAt,
                'updated_at'  => $createdAt,
            ]);
        }

        // ===== REVIEWS =====
        $reviewData = [
            ['product' => 0, 'customer' => 0, 'rating' => 5, 'comment' => 'Giày đẹp lắm, đi rất êm chân, giao hàng nhanh. Sẽ ủng hộ shop dài dài!'],
            ['product' => 1, 'customer' => 1, 'rating' => 5, 'comment' => 'Nike Dunk Panda đúng chuẩn hàng auth, form đẹp, size chuẩn như tư vấn.'],
            ['product' => 4, 'customer' => 2, 'rating' => 4, 'comment' => 'Adidas Samba chất lượng tốt, da mềm, đi thoải mái. Trừ 1 sao vì giao hơi chậm.'],
            ['product' => 5, 'customer' => 3, 'rating' => 5, 'comment' => 'Stan Smith classic không bao giờ lỗi mốt, shop tư vấn nhiệt tình, đóng gói cẩn thận.'],
            ['product' => 15,'customer' => 4, 'rating' => 5, 'comment' => 'Vans Old Skool đúng hàng, đế waffle chắc chắn, mang đi học rất hợp.'],
        ];

        foreach ($reviewData as $rd) {
            if (isset($products[$rd['product']])) {
                ProductReview::create([
                    'user_id'    => $customers[$rd['customer']]->id,
                    'product_id' => $products[$rd['product']]->id,
                    'rating'     => $rd['rating'],
                    'comment'    => $rd['comment'],
                    'created_at' => now()->subDays(rand(1, 20)),
                ]);
            }
        }

        // ===== CONTACTS =====
        $contactData = [
            ['customer' => 0, 'subject' => 'Hỏi về chính sách đổi trả',      'message' => 'Shop ơi cho mình hỏi nếu mua giày về đi không vừa thì có đổi size được không ạ? Thời gian đổi là bao lâu?', 'status' => 'replied', 'reply' => 'Chào bạn! Shop hỗ trợ đổi size trong vòng 7 ngày kể từ khi nhận hàng, miễn phí đổi lần đầu. Bạn chỉ cần giữ nguyên hộp và tag sản phẩm nhé!'],
            ['customer' => 1, 'subject' => 'Tư vấn chọn size giày Nike',      'message' => 'Mình thường đi size 40 giày Việt Nam, vậy mua Nike Air Force 1 nên chọn size mấy ạ?', 'status' => 'replied', 'reply' => 'Chào bạn! Nike Air Force 1 thường rộng hơn một chút, bạn đi size 40 VN thì nên chọn size 40 EU (US 7) là vừa nhất nhé!'],
            ['customer' => 2, 'subject' => 'Đơn hàng chưa nhận được',         'message' => 'Mình đặt đơn 3 ngày rồi mà chưa thấy cập nhật trạng thái giao hàng, shop kiểm tra giúp mình với ạ.', 'status' => 'read',    'reply' => null],
            ['customer' => 3, 'subject' => 'Hỏi về hàng auth và rep',         'message' => 'Shop bán hàng auth 100% không ạ? Mình muốn mua Adidas Samba nhưng sợ hàng fake.', 'status' => 'unread',  'reply' => null],
            ['customer' => 4, 'subject' => 'Góp ý về dịch vụ',               'message' => 'Shop giao hàng nhanh và đóng gói rất cẩn thận. Mình rất hài lòng, sẽ giới thiệu bạn bè ủng hộ shop!', 'status' => 'unread',  'reply' => null],
        ];

        foreach ($contactData as $cd) {
            Contact::create([
                'user_id'     => $customers[$cd['customer']]->id,
                'name'        => $customers[$cd['customer']]->name,
                'email'       => $customers[$cd['customer']]->email,
                'phone'       => $customers[$cd['customer']]->phone,
                'subject'     => $cd['subject'],
                'message'     => $cd['message'],
                'status'      => $cd['status'],
                'admin_reply' => $cd['reply'],
                'replied_at'  => $cd['reply'] ? now()->subDays(rand(1, 3)) : null,
            ]);
        }
    }

    private function generatePostContent(string $title): string
    {
        return "# {$title}\n\n"
            . "Trong thế giới thời trang sneaker ngày càng phát triển, việc lựa chọn đôi giày phù hợp không chỉ là nhu cầu mà còn là cách thể hiện cá tính bản thân.\n\n"
            . "## Tại sao nên chọn sản phẩm chính hãng?\n\n"
            . "Giày chính hãng không chỉ đảm bảo chất lượng vật liệu mà còn mang lại sự thoải mái tối ưu cho đôi chân. Các thương hiệu lớn đầu tư rất nhiều vào công nghệ đế giày và chất liệu upper để đảm bảo trải nghiệm tốt nhất.\n\n"
            . "## Cách bảo quản giày đúng cách\n\n"
            . "- Vệ sinh giày sau mỗi lần sử dụng\n"
            . "- Bảo quản trong hộp hoặc túi chống bụi\n"
            . "- Tránh để giày ở nơi ẩm ướt hoặc dưới ánh nắng trực tiếp\n"
            . "- Sử dụng cây giày để giữ form\n\n"
            . "## Kết luận\n\n"
            . "Đầu tư vào một đôi giày chất lượng là đầu tư cho sức khỏe và phong cách của bạn. Hãy đến TT Shop để khám phá bộ sưu tập giày chính hãng đa dạng nhất!";
    }
}
