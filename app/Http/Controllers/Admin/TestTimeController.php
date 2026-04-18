<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;

class TestTimeController extends Controller
{
    public function index()
    {
        $orders = Order::where('status', 'delivered')
            ->with(['statusHistories' => fn ($q) => $q->where('to_status', 'delivered')->latest()])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.test-time.index', compact('orders'));
    }

    public function fakeDeliveredAt(Request $request, Order $order)
    {
        $validated = $request->validate([
            'days_ago' => ['required', 'integer', 'min:0', 'max:365'],
        ]);

        $fakeDate = now()->subDays($validated['days_ago']);

        // Cập nhật bản ghi lịch sử trạng thái delivered
        OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', 'delivered')
            ->update(['created_at' => $fakeDate, 'updated_at' => $fakeDate]);

        return back()->with('success',
            "Đã fake thời gian giao hàng của đơn {$order->order_code} thành {$fakeDate->format('d/m/Y H:i')} ({$validated['days_ago']} ngày trước)."
        );
    }
}
