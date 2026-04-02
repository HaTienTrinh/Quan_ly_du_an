<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    private const SHIPPING_FEE = 30000;

    /**
     * Xem trang thanh toán
     */
    public function checkout()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống');
        }

        $total = collect($cart)->sum('subtotal');
        $shippingFee = self::SHIPPING_FEE;
        $totalAmount = $total + $shippingFee;

        $user = Auth::user();

        // Lấy địa chỉ gần nhất của người dùng nếu có
        $latestAddress = $user->addresses()->latest()->first();

        return view('customers.checkout.checkout', [
            'cart' => $cart,
            'total' => $total,
            'shippingFee' => $shippingFee,
            'totalAmount' => $totalAmount,
            'user' => $user,
            'latestAddress' => $latestAddress,
        ]);
    }

    /**
     * Lưu đơn hàng
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'receiver_province' => 'required|string|max:255',
            'receiver_district' => 'required|string|max:255',
            'receiver_ward' => 'required|string|max:255',
            'receiver_address_detail' => 'required|string',
            'payment_method' => 'required|in:cash,credit_card,bank_transfer',
            'note' => 'nullable|string',
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Giỏ hàng trống');
        }

        try {
            DB::beginTransaction();

            $subtotal = collect($cart)->sum('subtotal');

            // Tính phí vận chuyển (có thể tùy chỉnh)
            $shippingFee = self::SHIPPING_FEE; // 30,000 VND

            $totalAmount = $subtotal + $shippingFee;

            // Tạo mã đơn hàng
            $orderCode = 'ORD-' . date('YmdHis') . '-' . Auth::id();

            // Tạo đơn hàng
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => Auth::id(),
                'receiver_name' => $validated['receiver_name'],
                'receiver_phone' => $validated['receiver_phone'],
                'receiver_province' => $validated['receiver_province'],
                'receiver_district' => $validated['receiver_district'],
                'receiver_ward' => $validated['receiver_ward'],
                'receiver_address_detail' => $validated['receiver_address_detail'],
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'unpaid',
                'note' => $validated['note'] ?? null,
            ]);

            // Tạo các mục trong đơn hàng
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_thumbnail' => $item['product_thumbnail'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            // Xóa giỏ hàng
            Session::forget('cart');

            // Chuyển sang trang trạng thái đơn hàng sau khi đặt thành công
            return redirect()->route('orders.confirmation', $order->id)
                ->with('success', 'Đặt hàng thành công! Đang chuyển sang trang trạng thái đơn.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xác nhận đơn hàng
     */
    public function confirmation($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);

        // Kiểm tra quyền sở hữu
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('customers.checkout.confirmation', [
            'order' => $order,
        ]);
    }

    /**
     * Xem danh sách đơn hàng
     */
    public function index()
    {
        $orders = Auth::user()->orders()->orderByDesc('created_at')->paginate(10);

        return view('customers.orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Xem chi tiết đơn hàng
     */
    public function show($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);

        // Kiểm tra quyền sở hữu
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('customers.orders.show', [
            'order' => $order,
        ]);
    }
}
