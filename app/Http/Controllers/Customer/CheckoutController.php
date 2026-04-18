<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductSize;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    private const SHIPPING_FEE = 30000;

    public function index()
    {
        $cart = $this->getSelectedCart(request());

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $total           = collect($cart)->sum('subtotal');
        $shippingFee     = self::SHIPPING_FEE;
        $totalAmount     = $total + $shippingFee;
        $selectedItemIds = array_keys($cart);

        $user            = Auth::user();
        $savedAddresses  = $user->addresses()->orderByDesc('is_default')->latest()->get();
        $selectedAddress = $savedAddresses->firstWhere('is_default', true) ?? $savedAddresses->first();

        return view('customers.checkout.checkout', compact(
            'cart', 'total', 'shippingFee', 'totalAmount',
            'user', 'savedAddresses', 'selectedAddress', 'selectedItemIds'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'selected_items'          => 'required|array|min:1',
            'selected_items.*'        => 'string',
            'receiver_name'           => 'required|string|max:255',
            'receiver_phone'          => 'required|string|max:20',
            'receiver_province'       => 'required|string|max:255',
            'receiver_district'       => 'required|string|max:255',
            'receiver_ward'           => 'required|string|max:255',
            'receiver_address_detail' => 'required|string',
            'payment_method'          => 'required|in:cash,vnpay',
            'note'                    => 'nullable|string',
        ]);

        $cart = $this->getSelectedCart($request);

        if (empty($cart)) {
            return back()->with('error', 'Không tìm thấy sản phẩm đã chọn trong giỏ hàng.');
        }

        try {
            DB::beginTransaction();

            // Kiểm tra tồn kho
            foreach ($cart as $item) {
                $sizeId = $item['product_color_id'] ?? null;
                if ($sizeId) {
                    $size = ProductSize::lockForUpdate()->find($sizeId);
                    if (! $size || $size->stock < $item['quantity']) {
                        DB::rollBack();
                        return back()->with('error', "Sản phẩm «{$item['product_name']}» (size {$item['product_size_name']}) chỉ còn " . ($size?->stock ?? 0) . ' sản phẩm.');
                    }
                } else {
                    $product = Product::lockForUpdate()->find($item['product_id']);
                    if (! $product || $product->stock < $item['quantity']) {
                        DB::rollBack();
                        return back()->with('error', "Sản phẩm «{$item['product_name']}» chỉ còn " . ($product?->stock ?? 0) . ' sản phẩm.');
                    }
                }
            }

            $subtotal    = collect($cart)->sum('subtotal');
            $shippingFee = self::SHIPPING_FEE;
            $totalAmount = $subtotal + $shippingFee;

            $order = Order::create([
                'order_code'              => 'ORD-' . date('YmdHis') . '-' . Auth::id(),
                'user_id'                 => Auth::id(),
                'receiver_name'           => $validated['receiver_name'],
                'receiver_phone'          => $validated['receiver_phone'],
                'receiver_province'       => $validated['receiver_province'],
                'receiver_district'       => $validated['receiver_district'],
                'receiver_ward'           => $validated['receiver_ward'],
                'receiver_address_detail' => $validated['receiver_address_detail'],
                'subtotal'                => $subtotal,
                'shipping_fee'            => $shippingFee,
                'discount_amount'         => 0,
                'total_amount'            => $totalAmount,
                'status'                  => Order::STATUS_PENDING,
                'payment_method'          => $validated['payment_method'],
                'payment_status'          => 'unpaid',
                'note'                    => $validated['note'] ?? null,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'          => $order->id,
                    'product_id'        => $item['product_id'],
                    'product_color_id'  => $item['product_color_id'] ?? null,
                    'product_name'      => $item['product_name'],
                    'product_size_name' => $item['product_size_name'] ?? null,
                    'product_thumbnail' => $item['product_thumbnail'],
                    'unit_price'        => $item['unit_price'],
                    'quantity'          => $item['quantity'],
                    'subtotal'          => $item['subtotal'],
                ]);

                // Trừ stock
                $sizeId = $item['product_color_id'] ?? null;
                if ($sizeId) {
                    ProductSize::where('id', $sizeId)->decrement('stock', $item['quantity']);
                    $total = ProductSize::where('product_id', $item['product_id'])->sum('stock');
                    Product::where('id', $item['product_id'])->update(['stock' => $total]);
                } else {
                    Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
                }
            }

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'changed_by'  => Auth::id(),
                'from_status' => null,
                'to_status'   => Order::STATUS_PENDING,
                'note'        => 'Đơn hàng được tạo bởi khách hàng.',
            ]);

            DB::commit();

            // Xóa cart
            $remainingCart = Session::get('cart', []);
            foreach (array_keys($cart) as $key) {
                unset($remainingCart[$key]);
            }
            Session::put('cart', $remainingCart);

            // Nếu chọn VNPay → chuyển sang trang thanh toán
            if ($validated['payment_method'] === 'vnpay') {
                return redirect()->route('checkout.vnpay', $order);
            }

            return redirect()->route('orders.confirmation', $order)
                ->with('success', 'Đặt hàng thành công!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function confirmation(Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $order->loadMissing(['items.productSize']);

        return view('customers.checkout.confirmation', compact('order'));
    }

    // ===== VNPAY =====

    public function payVnpay(Order $order, VNPayService $vnpay)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order)->with('error', 'Đơn hàng này đã được thanh toán.');
        }

        $url = $vnpay->createPaymentUrl(
            orderId:   $order->id,
            amount:    (int) $order->total_amount,
            orderInfo: 'Thanh toan don hang ' . $order->order_code,
        );

        return redirect()->away($url);
    }

    public function vnpayReturn(Request $request, VNPayService $vnpay)
    {
        if (! $vnpay->verifyReturn($request)) {
            return redirect()->route('orders.index')->with('error', 'Chữ ký không hợp lệ.');
        }

        $order = Order::find($vnpay->getOrderId($request));

        if (! $order) {
            return redirect()->route('orders.index')->with('error', 'Không tìm thấy đơn hàng.');
        }

        if ($vnpay->isSuccess($request)) {
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'paid_at'        => now(),
                ]);
            }

            return redirect()->route('orders.confirmation', $order)
                ->with('success', 'Thanh toán VNPay thành công!');
        }

        return redirect()->route('orders.show', $order)
            ->with('error', 'Thanh toán thất bại. Mã lỗi: ' . $request->input('vnp_ResponseCode'));
    }

    private function getSelectedCart(Request $request): array
    {
        $cart          = Session::get('cart', []);
        $selectedItems = collect($request->input('selected_items', []))
            ->map(fn ($id) => (string) $id)
            ->filter()->unique()->values()->all();

        if ($selectedItems === []) {
            return [];
        }

        return collect($cart)
            ->filter(fn ($item, $key) => in_array((string) $key, $selectedItems, true))
            ->all();
    }
}
