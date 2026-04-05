# 🛒 TTM Shop - Shopping Cart & Checkout System

Hệ thống giỏ hàng và thanh toán đã được tạo hoàn chỉnh cho ứng dụng web bán giày TTM Shop.

## 📁 Files & Folders Created

### 1. Controllers

- **CartController** (`app/Http/Controllers/Customer/CartController.php`)
    - `index()` - Xem giỏ hàng
    - `add()` - Thêm sản phẩm vào giỏ
    - `update()` - Cập nhật số lượng
    - `remove()` - Xóa sản phẩm
    - `clear()` - Xóa toàn bộ giỏ

- **OrderController** (`app/Http/Controllers/Customer/OrderController.php`)
    - `checkout()` - Trang thanh toán
    - `store()` - Lưu đơn hàng
    - `confirmation()` - Xác nhận đơn hàng
    - `index()` - Danh sách đơn hàng
    - `show()` - Chi tiết đơn hàng

### 2. Views

- **Cart Pages**
    - `resources/views/customers/cart/index.blade.php` - Trang giỏ hàng

- **Checkout Pages**
    - `resources/views/customers/checkout/checkout.blade.php` - Trang thanh toán
    - `resources/views/customers/checkout/confirmation.blade.php` - Xác nhận đơn hàng

- **Order Pages**
    - `resources/views/customers/orders/index.blade.php` - Danh sách đơn hàng của khách hàng
    - `resources/views/customers/orders/show.blade.php` - Chi tiết đơn hàng

### 3. Updated Files

- **Routes** (`routes/web.php`) - Thêm routes cho cart và order
- **Layout** (`resources/views/customers/layouts/layout.blade.php`) - Cập nhật cart icon
- **Products Listing** (`resources/views/customers/products/index.blade.php`) - Thêm "Add to Cart" button

## 🔗 Routes Added

```
// Giỏ hàng (không cần đăng nhập)
GET    /cart               - Xem giỏ hàng (cart.index)
POST   /cart/add           - Thêm vào giỏ (cart.add)
PUT    /cart/{product_id}  - Cập nhật (cart.update)
DELETE /cart/{product_id}  - Xóa khỏi giỏ (cart.remove)
POST   /cart/clear         - Xóa trống giỏ (cart.clear)

// Đơn hàng (cần đăng nhập + role customer)
GET  /checkout                       - Trang thanh toán (checkout)
POST /orders                         - Tạo đơn hàng (orders.store)
GET  /orders                         - Danh sách đơn (orders.index)
GET  /orders/{order}/confirmation    - Xác nhận (orders.confirmation)
GET  /orders/{order}                 - Chi tiết đơn (orders.show)
```

## 🎨 Features

### Giỏ Hàng (Cart)

✅ Thêm sản phẩm vào giỏ hàng  
✅ Cập nhật số lượng sản phẩm  
✅ Xóa sản phẩm khỏi giỏ  
✅ Xem tổng giá trị giỏ hàng  
✅ Xóa trống giỏ hàng  
✅ Lưu giỏ hàng trong Session

### Thanh Toán (Checkout)

✅ Form nhập thông tin người nhận  
✅ Nhập địa chỉ giao hàng  
✅ Chọn phương thức thanh toán:

- Thanh toán khi nhận hàng (COD)
- Thẻ tín dụng
- Chuyển khoản ngân hàng  
  ✅ Thêm ghi chú cho đơn hàng  
  ✅ Hiển thị chi tiết đơn hàng

### Xác Nhận Đơn Hàng (Confirmation)

✅ Hiển thị mã đơn hàng  
✅ Chi tiết sản phẩm được đặt  
✅ Thông tin người nhận  
✅ Tổng tiền thanh toán  
✅ Trạng thái đơn hàng  
✅ Timeline trạng thái giao hàng

### Quản Lý Đơn Hàng

✅ Danh sách đơn hàng của khách hàng  
✅ Chi tiết từng đơn hàng  
✅ Theo dõi trạng thái đơn hàng  
✅ Xem thông tin giao hàng  
✅ Xem phương thức thanh toán

## 📊 Database Schema Used

**Orders Table** - Lưu thông tin đơn hàng

- order_code, user_id, receiver_name, receiver_phone
- receiver_address_detail, receiver_province, receiver_district, receiver_ward
- subtotal, shipping_fee, discount_amount, total_amount
- status, payment_method, payment_status
- created_at, updated_at

**OrderItems Table** - Lưu chi tiết sản phẩm trong đơn

- order_id, product_id, product_name, product_thumbnail
- unit_price, quantity, subtotal
- created_at, updated_at

## 🛠️ How It Works

### 1. Thêm vào giỏ hàng

- Người dùng bấm nút "Add to Cart" trên sản phẩm
- POST request được gửi tới `/cart/add` với product_id và quantity
- CartController lưu vào Session trên server

### 2. Xem giỏ hàng

- Truy cập `/cart` để xem tất cả sản phẩm đã thêm
- Có thể cập nhật số lượng hoặc xóa sản phẩm
- Hiển thị tổng tiền và phí vận chuyển

### 3. Thanh toán

- Bấm "Tiến hành thanh toán" từ trang giỏ hàng
- Điền thông tin người nhận và chọn phương thức thanh toán
- POST request tới `/orders` để tạo đơn hàng
- Dữ liệu từ Session được lưu vào database (Orders, OrderItems)
- Chuyển hướng tới trang xác nhận

### 4. Xác nhận

- Hiển thị chi tiết đơn hàng vừa tạo
- Khách hàng có thể xem lại thông tin
- Link để quay lại trang chủ hoặc xem chi tiết đơn hàng

### 5. Quản lý đơn hàng

- Khách hàng có thể xem danh sách tất cả đơn hàng
- Xem chi tiết từng đơn hàng
- Theo dõi trạng thái giao hàng

## 💻 Giao Diện

### Thiết kế

- ✅ Tương thích với theme hiện tại (dark theme + orange accent)
- ✅ Responsive trên mobile, tablet, desktop
- ✅ Sử dụng Tailwind CSS
- ✅ Glass morphism UI cho components

### Pages

1. **Giỏ hàng** - Danh sách sản phẩm + chi tiết thanh toán
2. **Thanh toán** - Form thông tin + preview đơn hàng
3. **Xác nhận** - Chi tiết đơn hàng + timeline trạng thái
4. **Danh sách đơn** - Pagination danh sách đơn hàng
5. **Chi tiết đơn** - Xem chi tiết 1 đơn hàng

## 🔐 Security

✅ Middleware `auth` - Yêu cầu đăng nhập cho checkout  
✅ Middleware `customer` - Chỉ khách hàng mới được checkout  
✅ Authorization check - Khách hàng chỉ xem được đơn hàng của mình  
✅ CSRF token - Bảo vệ POST requests

## 📝 Usage Examples

### 1. Add Product to Cart

```php
POST /cart/add
- product_id: 1
- quantity: 2
```

### 2. Update Cart

```php
PUT /cart/1
- quantity: 5
```

### 3. Remove from Cart

```php
DELETE /cart/1
```

### 4. Place Order

```php
POST /orders
- receiver_name: "Nguyễn Văn A"
- receiver_phone: "0123456789"
- receiver_province: "Hà Nội"
- receiver_district: "Ba Đình"
- receiver_ward: "Phúc Tân"
- receiver_address_detail: "123 Đường A, Hà Nội"
- payment_method: "cash"
- note: "Giao vào buổi sáng"
```

## 🚀 Next Steps (Optional)

- [ ] Thanh toán online integration (VNPay, etc.)
- [ ] Email notification khi đặt hàng
- [ ] SMS notification theo dõi đơn hàng
- [ ] Admin dashboard quản lý đơn hàng
- [ ] Promotional codes / Vouchers
- [ ] Customer reviews & ratings
- [ ] Wishlist functionality
- [ ] Order tracking with real-time updates

## ✨ Features Summary

| Feature                  | Status |
| ------------------------ | ------ |
| Add to Cart              | ✅     |
| Update Quantity          | ✅     |
| Remove Item              | ✅     |
| Clear Cart               | ✅     |
| Checkout Form            | ✅     |
| Order Confirmation       | ✅     |
| Order History            | ✅     |
| Order Tracking           | ✅     |
| Multiple Payment Methods | ✅     |
| Responsive Design        | ✅     |
| Session Management       | ✅     |
| Authorization            | ✅     |

---

**Created**: April 2, 2026  
**System**: TTM Shop E-Commerce Platform
