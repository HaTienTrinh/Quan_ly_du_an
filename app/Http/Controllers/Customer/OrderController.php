<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
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

        $orders = $ordersQuery->latest()->paginate(10)->withQueryString();

        $tabs = [
            'all'                      => ['label' => 'Tất cả',              'count' => (clone $baseQuery)->count()],
            Order::STATUS_PENDING      => ['label' => 'Chờ xác nhận',        'count' => (int) ($statusCounts[Order::STATUS_PENDING] ?? 0)],
            Order::STATUS_CONFIRMED    => ['label' => 'Đã xác nhận',         'count' => (int) ($statusCounts[Order::STATUS_CONFIRMED] ?? 0)],
            Order::STATUS_PROCESSING   => ['label' => 'Đang chuẩn bị hàng', 'count' => (int) ($statusCounts[Order::STATUS_PROCESSING] ?? 0)],
            Order::STATUS_SHIPPING     => ['label' => 'Đang giao hàng',      'count' => (int) ($statusCounts[Order::STATUS_SHIPPING] ?? 0)],
            Order::STATUS_DELIVERED    => ['label' => 'Đã giao thành công',  'count' => (int) ($statusCounts[Order::STATUS_DELIVERED] ?? 0)],
            Order::STATUS_RETURNED     => ['label' => 'Trả hàng',            'count' => $returnOrdersCount],
            Order::STATUS_CANCELLED    => ['label' => 'Đã hủy',              'count' => (int) ($statusCounts[Order::STATUS_CANCELLED] ?? 0)],
        ];

        return view('customers.orders.index', compact('orders', 'tabs', 'selectedStatus'));
    }

    public function show(Order $order)
    {
        $this->authorizeOwnedOrder($order);
        $order->loadMissing([
            'items.productSize',
            'items.returnRequest',
            'statusHistories.changedBy',
            'returnRequests.orderItem.productSize',
            'returnRequests.replacementOrder',
            'returnRequests.statusHistories.changedBy',
        ]);

        return view('customers.orders.show', compact('order'));
    }

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
                'status'       => Order::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancel_reason'=> $reason,
            ]);

            // Hoàn stock
            $order->loadMissing('items');
            foreach ($order->items as $item) {
                if ($item->product_color_id) {
                    ProductSize::where('id', $item->product_color_id)->increment('stock', $item->quantity);
                    $total = ProductSize::where('product_id', $item->product_id)->sum('stock');
                    Product::where('id', $item->product_id)->update(['stock' => $total]);
                } else {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'changed_by'  => Auth::id(),
                'from_status' => $fromStatus,
                'to_status'   => Order::STATUS_CANCELLED,
                'note'        => $reason,
            ]);
        });

        return redirect()->route('orders.show', $order)->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    public function receive(Order $order)
    {
        $this->authorizeOwnedOrder($order);

        if (! $order->canBeReceived()) {
            return back()->with('error', 'Đơn hàng này chưa thể xác nhận đã nhận.');
        }

        $fromStatus = $order->status;

        DB::transaction(function () use ($order, $fromStatus) {
            $attributes = ['status' => Order::STATUS_DELIVERED];

            if ($order->payment_status === 'unpaid' && $order->getRawOriginal('payment_method') === 'cod') {
                $attributes['payment_status'] = 'paid';
                $attributes['paid_at']        = now();
            }

            $order->update($attributes);

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'changed_by'  => Auth::id(),
                'from_status' => $fromStatus,
                'to_status'   => Order::STATUS_DELIVERED,
                'note'        => 'Khách hàng đã xác nhận nhận hàng thành công.',
            ]);
        });

        return redirect()->route('orders.show', $order)->with('success', 'Cảm ơn bạn đã xác nhận nhận hàng.');
    }

    public function reorder(Order $order)
    {
        $this->authorizeOwnedOrder($order);

        if ($order->status !== Order::STATUS_CANCELLED) {
            return back()->with('error', 'Chỉ có thể mua lại từ các đơn hàng đã hủy.');
        }

        $order->loadMissing(['items.productSize']);

        $products = Product::query()
            ->whereIn('id', $order->items->pluck('product_id')->filter()->unique()->values())
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

            $productSize = $item->productSize ?? ProductSize::where('product_id', $item->product_id)
                ->where('name', $item->product_size_name)
                ->first();

            $itemKey = $item->product_id . '-' . ($productSize?->id ?? 0);

            if (isset($cart[$itemKey])) {
                $cart[$itemKey]['quantity'] += $quantity;
            } else {
                $cart[$itemKey] = [
                    'item_key'          => $itemKey,
                    'product_id'        => $product->id,
                    'product_name'      => $product->name,
                    'product_color_id'  => $productSize?->id,
                    'product_size_name' => $productSize?->name ?? $item->product_size_name,
                    'product_thumbnail' => $product->thumbnail,
                    'unit_price'        => $product->price,
                    'quantity'          => $quantity,
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
}
