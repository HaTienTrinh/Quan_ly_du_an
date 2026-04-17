<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReturnRequestController extends Controller
{
    public function create(Order $order)
    {
        $this->authorizeOwnedOrder($order);
        $order->loadMissing(['items.returnRequest', 'items.product.sizes', 'returnRequests.orderItem']);

        if (! $order->canBeReturned()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Đơn hàng chỉ được gửi yêu cầu trả trong vòng 7 ngày từ lúc giao thành công.');
        }

        $eligibleItems = $order->items->filter(fn ($item) => $item->returnRequest === null)->values();

        if ($eligibleItems->isEmpty()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Tất cả sản phẩm trong đơn này đã có yêu cầu trả hàng.');
        }

        return view('customers.returns.create', [
            'order' => $order,
            'eligibleItems' => $eligibleItems,
        ]);
    }

    public function store(Request $request, Order $order)
    {
        $this->authorizeOwnedOrder($order);
        $order->loadMissing(['items.returnRequest', 'items.product.sizes']);

        if (! $order->canBeReturned()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Đơn hàng chỉ được gửi yêu cầu trả trong vòng 7 ngày từ lúc giao thành công.');
        }

        // Lấy sản phẩm được chọn trước để kiểm tra có biến thể không
        $selectedItem = $order->items->firstWhere('id', (int) $request->input('order_item_id'));
        $itemHasSizes = $selectedItem?->product?->sizes->isNotEmpty() ?? false;
        $isExchange   = $request->input('request_type') === 'exchange';

        $validated = $request->validate([
            'order_item_id'     => ['required', 'integer'],
            'request_type'      => ['required', 'in:refund,exchange'],
            'logistics_method'  => ['required', 'in:customer_ship,system_pickup'],
            'reason'            => ['required', 'string', 'max:2000'],
            'exchange_color_id' => Rule::when(
                $isExchange && $itemHasSizes,
                ['required', 'integer', 'exists:product_colors,id'],
                ['nullable']
            ),
            'evidences'   => ['nullable', 'array', 'max:5'],
            'evidences.*' => ['file', 'max:20480', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm'],
        ], [
            'exchange_color_id.required' => 'Vui lòng chọn size muốn đổi sang.',
            'evidences.*.mimes'          => 'Minh chứng chỉ hỗ trợ file ảnh hoặc video phổ biến.',
            'evidences.*.max'            => 'Mỗi file minh chứng không được vượt quá 20MB.',
        ]);

        $orderItem = $order->items->firstWhere('id', (int) $validated['order_item_id']);

        if (! $orderItem) {
            return back()->withErrors([
                'order_item_id' => 'Sản phẩm bạn chọn không thuộc đơn hàng này.',
            ])->withInput();
        }

        if ($orderItem->returnRequest !== null) {
            return back()->withErrors([
                'order_item_id' => 'Sản phẩm này đã có yêu cầu trả hàng trước đó.',
            ])->withInput();
        }

        $evidencePaths = collect($request->file('evidences', []))
            ->map(fn ($file) => $file->store('return-requests', 'public'))
            ->values()
            ->all();

        DB::transaction(function () use ($order, $orderItem, $validated, $evidencePaths) {
            $returnRequest = ReturnRequest::create([
                'order_id'          => $order->id,
                'order_item_id'     => $orderItem->id,
                'user_id'           => Auth::id(),
                'request_type'      => $validated['request_type'],
                'exchange_color_id' => $validated['exchange_color_id'] ?? null,
                'status'            => ReturnRequest::STATUS_PENDING,
                'reason'            => trim($validated['reason']),
                'logistics_method'  => $validated['logistics_method'],
                'evidence_paths'    => $evidencePaths,
            ]);

            ReturnRequestStatusHistory::create([
                'return_request_id' => $returnRequest->id,
                'changed_by' => Auth::id(),
                'from_status' => null,
                'to_status' => ReturnRequest::STATUS_PENDING,
                'note' => 'Khách hàng đã gửi yêu cầu trả hàng.',
            ]);
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Yêu cầu trả hàng đã được gửi. Chúng tôi sẽ kiểm duyệt sớm nhất.');
    }

    private function authorizeOwnedOrder(Order $order): void
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
