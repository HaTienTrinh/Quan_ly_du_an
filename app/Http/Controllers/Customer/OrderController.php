<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    private const SHIPPING_FEE = 30000;

    /**
     * Xem trang thanh toán.
     */
    public function checkout()
    {
        $cart = $this->getSelectedCart(request());

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $total = collect($cart)->sum('subtotal');
        $shippingFee = self::SHIPPING_FEE;
        $totalAmount = $total + $shippingFee;
        $selectedItemIds = array_keys($cart);

        $user = Auth::user();
        $savedAddresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $selectedAddress = $savedAddresses->firstWhere('is_default', true) ?? $savedAddresses->first();

        return view('customers.checkout.checkout', [
            'cart' => $cart,
            'total' => $total,
            'shippingFee' => $shippingFee,
            'totalAmount' => $totalAmount,
            'user' => $user,
            'savedAddresses' => $savedAddresses,
            'selectedAddress' => $selectedAddress,
            'selectedItemIds' => $selectedItemIds,
        ]);
    }

    /**
     * Lưu đơn hàng.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'string',
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'receiver_province' => 'required|string|max:255',
            'receiver_district' => 'required|string|max:255',
            'receiver_ward' => 'required|string|max:255',
            'receiver_address_detail' => 'required|string',
            'payment_method' => 'required|in:cash,credit_card,bank_transfer',
            'note' => 'nullable|string',
        ]);

        $cart = $this->getSelectedCart($request);

        if (empty($cart)) {
            return back()->with('error', 'Không tìm thấy sản phẩm đã chọn trong giỏ hàng.');
        }

        try {
            DB::beginTransaction();

            $subtotal = collect($cart)->sum('subtotal');
            $shippingFee = self::SHIPPING_FEE;
            $totalAmount = $subtotal + $shippingFee;

            $order = Order::create([
                'order_code' => 'ORD-' . date('YmdHis') . '-' . Auth::id(),
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
                'status' => Order::STATUS_PENDING,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'unpaid',
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'            => $order->id,
                    'product_id'          => $item['product_id'],
                    'product_color_id'    => $item['product_color_id'] ?? null,
                    'product_name'        => $item['product_name'],
                    'product_color_name'  => $item['product_color_name'] ?? null,
                    'product_color_hex'   => $item['product_color_hex'] ?? null,
                    'product_size'        => $item['product_size'] ?? null,
                    'product_thumbnail'   => $item['product_thumbnail'],
                    'unit_price'          => $item['unit_price'],
                    'quantity'            => $item['quantity'],
                    'subtotal'            => $item['subtotal'],
                ]);
            }

            $this->recordStatusHistory(
                order: $order,
                fromStatus: null,
                toStatus: Order::STATUS_PENDING,
                note: 'Đơn hàng được tạo bởi khách hàng.'
            );

            DB::commit();
            $remainingCart = Session::get('cart', []);

            foreach (array_keys($cart) as $productId) {
                unset($remainingCart[$productId]);
            }

            Session::put('cart', $remainingCart);

            return redirect()
                ->route('orders.confirmation', $order)
                ->with('success', 'Đặt hàng thành công. Bạn có thể theo dõi trạng thái đơn hàng ngay tại đây.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xác nhận đơn hàng sau khi đặt thành công.
     */
    public function confirmation(Order $order)
    {
        $this->authorizeOwnedOrder($order);
        $order->loadMissing(['items.productColor']);

        return view('customers.checkout.confirmation', [
            'order' => $order,
        ]);
    }

    /**
     * Xem danh sách đơn hàng.
     */
    public function index()
    {
        $allowedStatuses = [
            'all',
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPING,
            Order::STATUS_DELIVERED,
            Order::STATUS_RETURNED,
            Order::STATUS_CANCELLED,
        ];

        $selectedStatus = request()->string('status')->toString() ?: 'all';

        if (! in_array($selectedStatus, $allowedStatuses, true)) {
            $selectedStatus = 'all';
        }

        $baseQuery = Auth::user()->orders();

        $statusCounts = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $returnOrdersCount = (clone $baseQuery)
            ->where(function ($query) {
                $query->whereHas('returnRequests')
                    ->orWhere('status', Order::STATUS_RETURNED);
            })
            ->distinct('orders.id')
            ->count('orders.id');

        $ordersQuery = (clone $baseQuery)
            ->withCount(['items', 'returnRequests'])
            ->with([
                'statusHistories' => fn ($query) => $query
                    ->where('to_status', Order::STATUS_DELIVERED)
                    ->latest('created_at'),
                'latestReturnRequest',
            ]);

        if ($selectedStatus === Order::STATUS_RETURNED) {
            $ordersQuery->where(function ($query) {
                $query->whereHas('returnRequests')
                    ->orWhere('status', Order::STATUS_RETURNED);
            });
        } elseif ($selectedStatus !== 'all') {
            $ordersQuery->where('status', $selectedStatus);
        }

        $orders = $ordersQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $tabs = [
            'all' => [
                'label' => 'Tất cả',
                'count' => (clone $baseQuery)->count(),
            ],
            Order::STATUS_PENDING => [
                'label' => 'Chờ xác nhận',
                'count' => (int) ($statusCounts[Order::STATUS_PENDING] ?? 0),
            ],
            Order::STATUS_CONFIRMED => [
                'label' => 'Đã xác nhận',
                'count' => (int) ($statusCounts[Order::STATUS_CONFIRMED] ?? 0),
            ],
            Order::STATUS_PROCESSING => [
                'label' => 'Đang chuẩn bị hàng',
                'count' => (int) ($statusCounts[Order::STATUS_PROCESSING] ?? 0),
            ],
            Order::STATUS_SHIPPING => [
                'label' => 'Đang giao hàng',
                'count' => (int) ($statusCounts[Order::STATUS_SHIPPING] ?? 0),
            ],
            Order::STATUS_DELIVERED => [
                'label' => 'Đã giao thành công',
                'count' => (int) ($statusCounts[Order::STATUS_DELIVERED] ?? 0),
            ],
            Order::STATUS_RETURNED => [
                'label' => 'Trả hàng',
                'count' => $returnOrdersCount,
            ],
            Order::STATUS_CANCELLED => [
                'label' => 'Đã hủy',
                'count' => (int) ($statusCounts[Order::STATUS_CANCELLED] ?? 0),
            ],
        ];

        return view('customers.orders.index', [
            'orders' => $orders,
            'tabs' => $tabs,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    /**
     * Xem chi tiết đơn hàng.
     */
    public function show(Order $order)
    {
        $this->authorizeOwnedOrder($order);
        $order->loadMissing([
            'items.productColor',
            'items.returnRequest',
            'statusHistories.changedBy',
            'returnRequests.orderItem.productColor',
            'returnRequests.replacementOrder',
            'returnRequests.statusHistories.changedBy',
        ]);

        return view('customers.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Hủy đơn hàng.
     */
    public function cancel(Request $request, Order $order)
    {
        $this->authorizeOwnedOrder($order);

        if (! $order->canBeCancelled()) {
            return back()->with('error', 'Đơn hàng này không thể hủy ở thời điểm hiện tại.');
        }

        $validated = $request->validate([
            'cancel_reason' => 'nullable|string|max:255',
        ]);

        $fromStatus = $order->status;
        $reason = $validated['cancel_reason'] ?: 'Khách hàng chủ động hủy đơn.';

        DB::transaction(function () use ($order, $fromStatus, $reason) {
            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
            ]);

            $this->recordStatusHistory(
                order: $order,
                fromStatus: $fromStatus,
                toStatus: Order::STATUS_CANCELLED,
                note: $reason
            );
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    /**
     * Khách hàng xác nhận đã nhận hàng.
     */
    public function receive(Order $order)
    {
        $this->authorizeOwnedOrder($order);

        if (! $order->canBeReceived()) {
            return back()->with('error', 'Đơn hàng này chưa thể xác nhận đã nhận.');
        }

        $fromStatus = $order->status;

        DB::transaction(function () use ($order, $fromStatus) {
            $attributes = [
                'status' => Order::STATUS_DELIVERED,
            ];

            if (
                $order->payment_status === 'unpaid'
                && $order->getRawOriginal('payment_method') === 'cod'
            ) {
                $attributes['payment_status'] = 'paid';
                $attributes['paid_at'] = now();
            }

            $order->update($attributes);

            $this->recordStatusHistory(
                order: $order,
                fromStatus: $fromStatus,
                toStatus: Order::STATUS_DELIVERED,
                note: 'Khách hàng đã xác nhận nhận hàng thành công.'
            );
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Cảm ơn bạn đã xác nhận nhận hàng.');
    }

    /**
     * Mua lại sản phẩm từ đơn hàng cũ.
     */
    public function reorder(Order $order)
    {
        $this->authorizeOwnedOrder($order);

        if ($order->status !== Order::STATUS_CANCELLED) {
            return back()->with('error', 'Chỉ có thể mua lại từ các đơn hàng đã hủy.');
        }

        $order->loadMissing(['items.productColor']);

        $productIds = $order->items
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $cart = Session::get('cart', []);
        $addedCount = 0;
        $skippedProducts = [];

        foreach ($order->items as $item) {
            $product = $products->get($item->product_id);

            if (! $product || ! $product->isInStock()) {
                $skippedProducts[] = $item->product_name;
                continue;
            }

            $quantity = min($item->quantity, $product->stock);

            if ($quantity <= 0) {
                $skippedProducts[] = $item->product_name;
                continue;
            }

            $productColor = $this->resolveProductColorForReorder($item);
            $itemKey = $this->buildCartItemKey($product->id, $productColor?->id);

            if (isset($cart[$itemKey])) {
                $cart[$itemKey]['quantity'] += $quantity;
            } else {
                $cart[$itemKey] = [
                    'item_key' => $itemKey,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_color_id' => $productColor?->id,
                    'product_color_name' => $productColor?->name ?? $item->product_color_name,
                    'product_color_hex' => $productColor?->hex_code ?? $item->product_color_hex,
                    'product_thumbnail' => $product->thumbnail,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                ];
            }

            $cart[$itemKey]['subtotal'] = $cart[$itemKey]['unit_price'] * $cart[$itemKey]['quantity'];
            $addedCount++;
        }

        Session::put('cart', $cart);

        if ($addedCount === 0) {
            return back()->with('error', 'Không thể mua lại vì các sản phẩm trong đơn hiện không còn khả dụng.');
        }

        $message = 'Đã thêm sản phẩm từ đơn hàng cũ vào giỏ hàng.';

        if ($skippedProducts !== []) {
            $message .= ' Một số sản phẩm không còn khả dụng: ' . implode(', ', array_unique($skippedProducts)) . '.';
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    private function authorizeOwnedOrder(Order $order): void
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }

    private function recordStatusHistory(
        Order $order,
        ?string $fromStatus,
        string $toStatus,
        ?string $note = null
    ): void {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'changed_by' => Auth::id(),
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'note' => $note,
        ]);
    }

    private function getSelectedCart(Request $request): array
    {
        $cart = Session::get('cart', []);
        $selectedItems = collect($request->input('selected_items', []))
            ->map(fn ($id) => (string) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($selectedItems === []) {
            return [];
        }

        return collect($cart)
            ->filter(fn ($item, $productId) => in_array((string) $productId, $selectedItems, true))
            ->all();
    }

    private function resolveProductColorForReorder(OrderItem $item): ?ProductColor
    {
        if ($item->productColor) {
            return $item->productColor;
        }

        if (! $item->product_id || ! $item->product_color_name) {
            return null;
        }

        return ProductColor::query()
            ->where('product_id', $item->product_id)
            ->where('name', $item->product_color_name)
            ->first();
    }

    private function buildCartItemKey(int $productId, ?int $productColorId): string
    {
        return $productId . '-' . ($productColorId ?? 0);
    }
}
