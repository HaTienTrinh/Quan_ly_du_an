<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ProductColor;
use App\Models\Refund;
use App\Models\Reship;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $statusOptions = [
            '' => 'Tất cả trạng thái',
            ReturnRequest::STATUS_PENDING => 'Chờ xử lý',
            ReturnRequest::STATUS_APPROVED => 'Đã duyệt',
            ReturnRequest::STATUS_SHIPPING_BACK => 'Đang vận chuyển về',
            ReturnRequest::STATUS_RECEIVED => 'Đã nhận hàng trả về',
            ReturnRequest::STATUS_INSPECTING => 'Đang kiểm tra',
            ReturnRequest::STATUS_REFUNDED => 'Đã hoàn tiền',
            ReturnRequest::STATUS_EXCHANGED => 'Đã xác nhận đổi hàng',
            ReturnRequest::STATUS_COMPLETED => 'Hoàn tất',
            ReturnRequest::STATUS_REJECTED => 'Đã từ chối',
        ];

        $query = ReturnRequest::query()
            ->with(['user', 'order.user', 'orderItem'])
            ->latest();

        if ($request->filled('q')) {
            $keyword = trim((string) $request->string('q'));

            $query->where(function ($builder) use ($keyword) {
                $builder->whereHas('order', function ($orderQuery) use ($keyword) {
                    $orderQuery->where('order_code', 'like', '%' . $keyword . '%')
                        ->orWhere('receiver_name', 'like', '%' . $keyword . '%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $keyword . '%'));
                })->orWhereHas('orderItem', fn ($itemQuery) => $itemQuery->where('product_name', 'like', '%' . $keyword . '%'));
            });
        }

        if ($request->filled('status') && array_key_exists((string) $request->status, $statusOptions)) {
            $query->where('status', $request->status);
        }

        $returnRequests = $query->paginate(12)->withQueryString();

        return view('admin.return-requests.index', [
            'returnRequests' => $returnRequests,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function show(ReturnRequest $returnRequest)
    {
        $this->loadDetails($returnRequest);

        return view('admin.return-requests.show', [
            'returnRequest' => $returnRequest,
        ]);
    }

    public function approve(Request $request, ReturnRequest $returnRequest)
    {
        if (! $returnRequest->canBeApproved()) {
            return back()->with('error', 'Yêu cầu này hiện không thể duyệt.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        $this->transition(
            returnRequest: $returnRequest,
            toStatus: ReturnRequest::STATUS_APPROVED,
            note: 'Đã duyệt yêu cầu trả hàng.',
            extraAttributes: [
                'admin_note' => $validated['admin_note'] ?? null,
                'approved_at' => now(),
                'rejection_reason' => null,
            ],
        );

        return back()->with('success', 'Đã duyệt yêu cầu trả hàng.');
    }

    public function reject(Request $request, ReturnRequest $returnRequest)
    {
        if (! $returnRequest->canBeRejected()) {
            return back()->with('error', 'Yêu cầu này hiện không thể từ chối.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $this->transition(
            returnRequest: $returnRequest,
            toStatus: ReturnRequest::STATUS_REJECTED,
            note: 'Đã từ chối yêu cầu trả hàng. Lý do: ' . trim($validated['rejection_reason']),
            extraAttributes: [
                'rejection_reason' => trim($validated['rejection_reason']),
                'admin_note' => $validated['admin_note'] ?? null,
                'resolved_at' => now(),
            ],
        );

        return back()->with('success', 'Đã từ chối yêu cầu trả hàng.');
    }

    public function shippingBack(Request $request, ReturnRequest $returnRequest)
    {
        if (! $returnRequest->canBeMarkedShippingBack()) {
            return back()->with('error', 'Chỉ có thể chuyển sang trạng thái đang vận chuyển về sau khi yêu cầu đã được duyệt.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        $this->transition(
            returnRequest: $returnRequest,
            toStatus: ReturnRequest::STATUS_SHIPPING_BACK,
            note: 'Khách đang gửi hàng trả về.',
            extraAttributes: [
                'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
                'shipping_back_at' => now(),
            ],
        );

        return back()->with('success', 'Đã chuyển yêu cầu sang trạng thái đang vận chuyển về.');
    }

    public function receive(Request $request, ReturnRequest $returnRequest)
    {
        if (! $returnRequest->canBeMarkedReceived()) {
            return back()->with('error', 'Chỉ có thể đánh dấu đã nhận hàng sau bước vận chuyển về.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        $this->transition(
            returnRequest: $returnRequest,
            toStatus: ReturnRequest::STATUS_RECEIVED,
            note: 'Kho đã nhận hàng trả về.',
            extraAttributes: [
                'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
                'received_at' => now(),
            ],
        );

        return back()->with('success', 'Đã ghi nhận hàng trả về kho.');
    }

    public function inspect(Request $request, ReturnRequest $returnRequest)
    {
        if (! $returnRequest->canBeMarkedInspecting()) {
            return back()->with('error', 'Chỉ có thể chuyển sang bước kiểm tra sau khi hàng đã được nhận.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        $this->transition(
            returnRequest: $returnRequest,
            toStatus: ReturnRequest::STATUS_INSPECTING,
            note: 'Đang kiểm tra hàng trả về.',
            extraAttributes: [
                'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
                'inspecting_at' => now(),
            ],
        );

        return back()->with('success', 'Đã chuyển sang bước kiểm tra hàng.');
    }

    /**
     * Xử lý kết quả kiểm tra hàng (INSPECTION).
     *
     * Đây là bước bắt buộc, admin bấm 1 trong 2 nút:
     *   - "Hàng hợp lệ"  (verdict = valid)
     *   - "Hàng gian lận" (verdict = fraud)
     *
     * Luồng xử lý:
     *   GIAN LẬN:
     *     → Tạo Inspection (is_valid = false)
     *     → status = rejected
     *     → user.violation_count + 1
     *     → Tạo Reship (gửi trả hàng lại cho khách)
     *
     *   HỢP LỆ + type = refund:
     *     → Tạo Inspection (is_valid = true)
     *     → Tạo Refund
     *     → status = refunded
     *
     *   HỢP LỆ + type = exchange:
     *     → Tạo Inspection (is_valid = true)
     *     → Tạo Reship (gửi hàng đổi cho khách)
     *     → status = exchanged
     */
    public function processInspection(Request $request, ReturnRequest $returnRequest)
    {
        if ($returnRequest->status !== ReturnRequest::STATUS_INSPECTING) {
            return back()->with('error', 'Yêu cầu này chưa ở bước kiểm tra hàng.');
        }

        if ($returnRequest->inspection()->exists()) {
            return back()->with('error', 'Yêu cầu này đã được kiểm tra rồi.');
        }

        $validated = $request->validate([
            'verdict'    => ['required', 'in:valid,fraud'],
            'note'       => ['nullable', 'string', 'max:1000'],
            'admin_note' => ['nullable', 'string'],
            // Số tiền hoàn — chỉ bắt buộc khi hợp lệ + refund
            'amount' => [
                'nullable', 'numeric', 'min:0',
                Rule::requiredIf(
                    fn () => $request->verdict === 'valid'
                          && $returnRequest->request_type === ReturnRequest::TYPE_REFUND
                ),
            ],
            // Màu/size gửi lại — chỉ bắt buộc khi hợp lệ + exchange
            'replacement_color_id' => [
                'nullable', 'integer',
                Rule::requiredIf(
                    fn () => $request->verdict === 'valid'
                          && $returnRequest->request_type === ReturnRequest::TYPE_EXCHANGE
                ),
                Rule::exists('product_colors', 'id')->where(
                    'product_id', $returnRequest->orderItem->product_id
                ),
            ],
        ]);

        $isFraud = $validated['verdict'] === 'fraud';
        $replacementOrder = null;

        DB::transaction(function () use ($returnRequest, $validated, $isFraud, &$replacementOrder) {
            $fromStatus = $returnRequest->status;

            // Bước 1: Lưu kết quả kiểm tra
            Inspection::create([
                'return_id' => $returnRequest->id,
                'is_valid'  => ! $isFraud,
                'note'      => $validated['note'] ?? null,
            ]);

            if ($isFraud) {
                // ===== GIAN LẬN: từ chối + tăng vi phạm + tạo đơn gửi TRẢ hàng lại khách =====
                $returnRequest->user->increment('violation_count');

                // Tạo đơn mới: gửi trả đúng hàng cũ (không đổi màu/size)
                $replacementOrder = $this->createReplacementOrder($returnRequest, null, 'fraud');

                $returnRequest->update([
                    'status'               => ReturnRequest::STATUS_REJECTED,
                    'replacement_order_id' => $replacementOrder->id,
                    'admin_note'           => $validated['admin_note'] ?? $returnRequest->admin_note,
                    'resolved_at'          => now(),
                ]);

                ReturnRequestStatusHistory::create([
                    'return_request_id' => $returnRequest->id,
                    'changed_by'        => Auth::id(),
                    'from_status'       => $fromStatus,
                    'to_status'         => ReturnRequest::STATUS_REJECTED,
                    'note'              => 'Phát hiện gian lận. Tạo đơn #' . $replacementOrder->order_code . ' để gửi trả hàng lại khách.',
                ]);

            } elseif ($returnRequest->request_type === ReturnRequest::TYPE_REFUND) {
                // ===== HỢP LỆ + REFUND: tạo Refund + tạo đơn ghi nhận hoàn tiền =====
                Refund::create([
                    'return_id' => $returnRequest->id,
                    'amount'    => $validated['amount'],
                    'status'    => 'pending',
                ]);

                // Tạo đơn mới với total_amount = 0 (hoàn tiền, không gửi hàng)
                $replacementOrder = $this->createReplacementOrder($returnRequest, null, 'refund');

                $returnRequest->update([
                    'status'               => ReturnRequest::STATUS_REFUNDED,
                    'replacement_order_id' => $replacementOrder->id,
                    'admin_note'           => $validated['admin_note'] ?? $returnRequest->admin_note,
                    'resolved_at'          => now(),
                ]);

                ReturnRequestStatusHistory::create([
                    'return_request_id' => $returnRequest->id,
                    'changed_by'        => Auth::id(),
                    'from_status'       => $fromStatus,
                    'to_status'         => ReturnRequest::STATUS_REFUNDED,
                    'note'              => 'Hàng hợp lệ. Tạo đơn #' . $replacementOrder->order_code . ' để ghi nhận hoàn tiền ' . number_format($validated['amount']) . 'đ.',
                ]);

            } else {
                // ===== HỢP LỆ + EXCHANGE: tạo đơn gửi hàng đổi với màu/size khách chọn =====
                $newColor = ProductColor::find($validated['replacement_color_id']);

                $replacementOrder = $this->createReplacementOrder($returnRequest, $newColor, 'exchange');

                $returnRequest->update([
                    'status'               => ReturnRequest::STATUS_EXCHANGED,
                    'replacement_order_id' => $replacementOrder->id,
                    'admin_note'           => $validated['admin_note'] ?? $returnRequest->admin_note,
                    'resolved_at'          => now(),
                ]);

                ReturnRequestStatusHistory::create([
                    'return_request_id' => $returnRequest->id,
                    'changed_by'        => Auth::id(),
                    'from_status'       => $fromStatus,
                    'to_status'         => ReturnRequest::STATUS_EXCHANGED,
                    'note'              => 'Hàng hợp lệ. Tạo đơn #' . $replacementOrder->order_code . ' gửi hàng đổi (' . $newColor->display_name . ') cho khách.',
                ]);
            }
        });

        return redirect()
            ->route('admin.orders.show', $replacementOrder)
            ->with('success', $isFraud
                ? 'Gian lận! Đã tạo đơn gửi trả hàng lại khách.'
                : ($returnRequest->request_type === ReturnRequest::TYPE_REFUND
                    ? 'Hợp lệ. Đã tạo đơn hoàn tiền.'
                    : 'Hợp lệ. Đã tạo đơn gửi hàng đổi.')
            );
    }

    public function refund(Request $request, ReturnRequest $returnRequest)
    {
        if (! $returnRequest->canBeRefunded()) {
            return back()->with('error', 'Yêu cầu này hiện không thể hoàn tiền.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        $this->transition(
            returnRequest: $returnRequest,
            toStatus: ReturnRequest::STATUS_REFUNDED,
            note: 'Đã xử lý hoàn tiền cho khách hàng.',
            extraAttributes: [
                'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
                'resolved_at' => now(),
            ],
        );

        return back()->with('success', 'Đã xử lý hoàn tiền.');
    }

    public function exchange(Request $request, ReturnRequest $returnRequest)
    {
        if (! $returnRequest->canBeExchanged()) {
            return back()->with('error', 'Yêu cầu này hiện không thể đổi hàng.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        $this->transition(
            returnRequest: $returnRequest,
            toStatus: ReturnRequest::STATUS_EXCHANGED,
            note: 'Đã xác nhận xử lý đổi hàng cho khách.',
            extraAttributes: [
                'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
                'resolved_at' => now(),
            ],
        );

        return back()->with('success', 'Đã chuyển yêu cầu sang trạng thái đã xác nhận đổi hàng.');
    }

    public function complete(Request $request, ReturnRequest $returnRequest)
    {
        if (! $returnRequest->canBeCompleted()) {
            return back()->with('error', 'Chỉ có thể hoàn tất sau khi đã hoàn tiền hoặc xác nhận đổi hàng.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
            'replacement_product_color_id' => [
                Rule::requiredIf(function () use ($returnRequest) {
                    return $returnRequest->request_type === ReturnRequest::TYPE_EXCHANGE
                        && ! $returnRequest->replacement_order_id
                        && $returnRequest->orderItem?->product?->colors?->isNotEmpty();
                }),
                'nullable',
                'integer',
                Rule::exists('product_colors', 'id')->where(function ($query) use ($returnRequest) {
                    $query->where('product_id', $returnRequest->orderItem->product_id);
                }),
            ],
        ]);

        $replacementOrder = null;

        DB::transaction(function () use ($returnRequest, $validated, &$replacementOrder) {
            $fromStatus = $returnRequest->status;

            $returnRequest->update([
                'status' => ReturnRequest::STATUS_COMPLETED,
                'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
                'completed_at' => now(),
            ]);

            ReturnRequestStatusHistory::create([
                'return_request_id' => $returnRequest->id,
                'changed_by' => Auth::id(),
                'from_status' => $fromStatus,
                'to_status' => ReturnRequest::STATUS_COMPLETED,
                'note' => 'Yêu cầu trả hàng đã hoàn tất.',
            ]);

            if ($returnRequest->request_type === ReturnRequest::TYPE_EXCHANGE && ! $returnRequest->replacement_order_id) {
                $replacementOrder = $this->createReplacementOrder(
                    $returnRequest,
                    $this->resolveReplacementColor($returnRequest, $validated['replacement_product_color_id'] ?? null)
                );

                $returnRequest->update([
                    'replacement_order_id' => $replacementOrder->id,
                ]);
            }
        });

        if ($replacementOrder !== null) {
            return redirect()
                ->route('admin.orders.show', $replacementOrder)
                ->with('success', 'Đã hoàn tất yêu cầu trả hàng và tạo đơn mới để gửi lại hàng cho khách.');
        }

        return back()->with('success', 'Đã hoàn tất yêu cầu trả hàng.');
    }

    private function loadDetails(ReturnRequest $returnRequest): void
    {
        $returnRequest->load([
            'user',
            'order.user',
            'orderItem.product.colors',
            'orderItem.productColor',
            'exchangeColor',           // màu/size khách muốn đổi sang
            'replacementOrder',
            'statusHistories.changedBy',
            'inspection',
            'reship',
            'refund',
        ]);
    }

    private function transition(
        ReturnRequest $returnRequest,
        string $toStatus,
        string $note,
        array $extraAttributes = []
    ): void {
        DB::transaction(function () use ($returnRequest, $toStatus, $note, $extraAttributes) {
            $fromStatus = $returnRequest->status;

            $returnRequest->update(array_merge($extraAttributes, [
                'status' => $toStatus,
            ]));

            ReturnRequestStatusHistory::create([
                'return_request_id' => $returnRequest->id,
                'changed_by' => Auth::id(),
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'note' => $note,
            ]);
        });
    }

    private function createReplacementOrder(
        ReturnRequest $returnRequest,
        ?ProductColor $newColor,
        string $reason  // 'fraud' | 'refund' | 'exchange'
    ): Order {
        $sourceOrder = $returnRequest->order;
        $sourceItem  = $returnRequest->orderItem;

        // Fraud và exchange → gửi hàng → status = confirmed
        // Refund → chỉ ghi nhận hoàn tiền, không gửi hàng → status = cancelled
        $orderStatus = $reason === 'refund' ? Order::STATUS_CANCELLED : Order::STATUS_CONFIRMED;

        $noteMap = [
            'fraud'    => 'Gửi TRẢ hàng lại khách (phát hiện gian lận) từ yêu cầu #' . $returnRequest->id,
            'refund'   => 'Ghi nhận hoàn tiền từ yêu cầu #' . $returnRequest->id,
            'exchange' => 'Gửi hàng ĐỔI cho khách từ yêu cầu #' . $returnRequest->id,
        ];

        $replacementOrder = Order::create([
            'order_code'             => Order::generateOrderCode(),
            'user_id'                => $sourceOrder->user_id,
            'receiver_name'          => $sourceOrder->receiver_name,
            'receiver_phone'         => $sourceOrder->receiver_phone,
            'receiver_province'      => $sourceOrder->receiver_province,
            'receiver_district'      => $sourceOrder->receiver_district,
            'receiver_ward'          => $sourceOrder->receiver_ward,
            'receiver_address_detail'=> $sourceOrder->receiver_address_detail,
            'subtotal'               => $sourceItem->subtotal,
            'shipping_fee'           => 0,
            'discount_amount'        => $sourceItem->subtotal,
            'total_amount'           => 0,
            'status'                 => $orderStatus,
            'payment_method'         => $sourceOrder->getRawOriginal('payment_method'),
            'payment_status'         => 'paid',
            'paid_at'                => now(),
            'note'                   => $noteMap[$reason],
            'admin_note'             => 'Tạo tự động từ đơn ' . $sourceOrder->order_code,
            'confirmed_by'           => Auth::id(),
            'confirmed_at'           => $orderStatus === Order::STATUS_CONFIRMED ? now() : null,
        ]);

        // Màu/size của item trong đơn mới:
        //   fraud   → giữ nguyên màu/size cũ
        //   refund  → giữ nguyên (chỉ ghi nhận)
        //   exchange → dùng màu/size khách chọn ($newColor)
        $colorToUse = ($reason === 'exchange' && $newColor) ? $newColor : $sourceItem->productColor;

        OrderItem::create([
            'order_id'           => $replacementOrder->id,
            'product_id'         => $sourceItem->product_id,
            'product_color_id'   => $colorToUse?->id,
            'product_name'       => $sourceItem->product_name,
            'product_color_name' => $colorToUse?->name ?? $sourceItem->product_color_name,
            'product_color_hex'  => $colorToUse?->hex_code ?? $sourceItem->product_color_hex,
            'product_size'       => $colorToUse?->size ?? $sourceItem->product_size,
            'product_thumbnail'  => $sourceItem->product_thumbnail,
            'unit_price'         => $sourceItem->unit_price,
            'quantity'           => $sourceItem->quantity,
            'subtotal'           => $sourceItem->subtotal,
        ]);

        OrderStatusHistory::create([
            'order_id'    => $replacementOrder->id,
            'changed_by'  => Auth::id(),
            'from_status' => null,
            'to_status'   => $orderStatus,
            'note'        => $noteMap[$reason],
        ]);

        return $replacementOrder;
    }
}
