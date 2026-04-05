<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $this->loadOrderDetails($order);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function confirm(Request $request, Order $order)
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'Chỉ có thể xác nhận đơn hàng đang chờ xác nhận.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        $fromStatus = $order->status;
        $adminNote = trim((string) ($validated['admin_note'] ?? ''));

        DB::transaction(function () use ($order, $fromStatus, $adminNote) {
            $payload = [
                'status' => Order::STATUS_CONFIRMED,
                'confirmed_by' => Auth::id(),
                'confirmed_at' => now(),
                'cancelled_at' => null,
                'cancel_reason' => null,
            ];

            if ($adminNote !== '') {
                $payload['admin_note'] = $adminNote;
            }

            $order->update($payload);

            $historyNote = 'Quản trị viên đã xác nhận đơn hàng.';

            if ($adminNote !== '') {
                $historyNote .= ' Ghi chú: ' . $adminNote;
            }

            $this->recordStatusHistory(
                order: $order,
                fromStatus: $fromStatus,
                toStatus: Order::STATUS_CONFIRMED,
                note: $historyNote,
            );
        });

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Đã xác nhận đơn hàng thành công.');
    }

    public function cancel(Request $request, Order $order)
    {
        if (! $order->canBeCancelled()) {
            return back()->with('error', 'Đơn hàng này không thể hủy ở thời điểm hiện tại.');
        }

        $validated = $request->validate([
            'cancel_reason' => ['required', 'string', 'max:255'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $fromStatus = $order->status;
        $adminNote = trim((string) ($validated['admin_note'] ?? ''));
        $cancelReason = trim((string) $validated['cancel_reason']);

        DB::transaction(function () use ($order, $fromStatus, $cancelReason, $adminNote) {
            $payload = [
                'status' => Order::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancel_reason' => $cancelReason,
            ];

            if ($adminNote !== '') {
                $payload['admin_note'] = $adminNote;
            }

            $order->update($payload);

            $historyNote = 'Quản trị viên hủy đơn. Lý do: ' . $cancelReason;

            if ($adminNote !== '') {
                $historyNote .= ' Ghi chú: ' . $adminNote;
            }

            $this->recordStatusHistory(
                order: $order,
                fromStatus: $fromStatus,
                toStatus: Order::STATUS_CANCELLED,
                note: $historyNote,
            );
        });

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Đã hủy đơn hàng thành công.');
    }

    private function loadOrderDetails(Order $order): void
    {
        $order->load([
            'user',
            'confirmedBy',
            'items.product',
            'statusHistories.changedBy',
        ]);
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
}
