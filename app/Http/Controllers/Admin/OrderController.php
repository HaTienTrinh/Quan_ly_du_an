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
            '' => 'Tat ca trang thai',
            Order::STATUS_PENDING => 'Cho xac nhan',
            Order::STATUS_CONFIRMED => 'Da xac nhan',
            Order::STATUS_PROCESSING => 'Dang chuan bi',
            Order::STATUS_SHIPPING => 'Dang giao',
            Order::STATUS_DELIVERED => 'Hoan thanh',
            Order::STATUS_CANCELLED => 'Da huy',
            Order::STATUS_RETURNED => 'Hoan tra',
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
                'label' => 'Tong don hang',
                'value' => Order::count(),
                'icon' => 'bi-receipt',
                'class' => 'text-primary bg-primary-subtle',
            ],
            [
                'label' => 'Cho xu ly',
                'value' => (int) ($statusCounts[Order::STATUS_PENDING] ?? 0),
                'icon' => 'bi-hourglass-split',
                'class' => 'text-warning bg-warning-subtle',
            ],
            [
                'label' => 'Dang giao',
                'value' => (int) ($statusCounts[Order::STATUS_SHIPPING] ?? 0),
                'icon' => 'bi-truck',
                'class' => 'text-info bg-info-subtle',
            ],
            [
                'label' => 'Hoan thanh',
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
}
