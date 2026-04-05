<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $statusOptions = [
            '' => 'Tất cả trạng thái',
            Order::STATUS_PENDING => 'Chờ xác nhận',
            Order::STATUS_CONFIRMED => 'Đã xác nhận',
            Order::STATUS_PROCESSING => 'Đang chuẩn bị',
            Order::STATUS_SHIPPING => 'Đang giao',
            Order::STATUS_DELIVERED => 'Hoàn thành',
            Order::STATUS_CANCELLED => 'Đã hủy',
            Order::STATUS_RETURNED => 'Hoàn trả',
        ];

        $query = Order::query()
            ->with(['user'])
            ->withCount('items');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->string('q'));

            $query->where(function ($builder) use ($keyword) {
                $builder->where('order_code', 'like', '%' . $keyword . '%')
                    ->orWhere('receiver_name', 'like', '%' . $keyword . '%')
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($request->filled('status') && array_key_exists($request->status, $statusOptions)) {
            $query->where('status', $request->status);
        }

        $orders = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $summaryCards = [
            [
                'label' => 'Tổng đơn hàng',
                'value' => Order::count(),
                'icon' => 'bi-receipt',
                'class' => 'text-primary bg-primary-subtle',
            ],
            [
                'label' => 'Chờ xử lý',
                'value' => (int) ($statusCounts[Order::STATUS_PENDING] ?? 0),
                'icon' => 'bi-hourglass-split',
                'class' => 'text-warning bg-warning-subtle',
            ],
            [
                'label' => 'Đang giao',
                'value' => (int) ($statusCounts[Order::STATUS_SHIPPING] ?? 0),
                'icon' => 'bi-truck',
                'class' => 'text-info bg-info-subtle',
            ],
            [
                'label' => 'Hoàn thành',
                'value' => (int) ($statusCounts[Order::STATUS_DELIVERED] ?? 0),
                'icon' => 'bi-check2-circle',
                'class' => 'text-success bg-success-subtle',
            ],
        ];

        return view('admin.orders.index', [
            'orders' => $orders,
            'statusOptions' => $statusOptions,
            'summaryCards' => $summaryCards,
        ]);
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'confirmedBy',
            'items.product',
            'statusHistories.changedBy',
        ]);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }
}
