<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;

class DevController extends Controller
{
    public function index()
    {
        $orders = Order::where('status', 'delivered')
            ->with([
                'user',
                'statusHistories' => fn ($q) => $q->where('to_status', 'delivered'),
            ])
            ->latest()
            ->get();

        return view('admin.dev.index', compact('orders'));
    }

    public function fakeDeliveredAt(Request $request, Order $order)
    {
        $validated = $request->validate([
            'days_ago' => ['required', 'integer', 'min:0', 'max:365'],
        ]);

        $fakeDate = now()->subDays($validated['days_ago']);

        OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', 'delivered')
            ->update(['created_at' => $fakeDate, 'updated_at' => $fakeDate]);

        $label = $validated['days_ago'] === 0 ? 'hôm nay' : "{$validated['days_ago']} ngày trước ({$fakeDate->format('d/m/Y')})";

        return back()->with('success', "Đơn {$order->order_code}: đã fake thời gian giao hàng thành {$label}.");
    }
}
