<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $statusOptions = [
            '' => 'Tất cả trạng thái',
            ReturnRequest::STATUS_PENDING => 'Chờ xử lý',
            ReturnRequest::STATUS_APPROVED => 'Đã duyệt',
            ReturnRequest::STATUS_SHIPPING_BACK => 'Đang vận chuyển về',
            ReturnRequest::STATUS_RECEIVED => 'Đã nhận hàng',
            ReturnRequest::STATUS_INSPECTING => 'Đang kiểm tra',
            ReturnRequest::STATUS_REFUNDED => 'Đã hoàn tiền',
            ReturnRequest::STATUS_EXCHANGED => 'Đã đổi hàng',
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
            'logistics_method' => ['required', 'in:customer_ship,system_pickup'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $this->transition(
            returnRequest: $returnRequest,
            toStatus: ReturnRequest::STATUS_APPROVED,
            note: 'Đã duyệt yêu cầu trả hàng.',
            extraAttributes: [
                'logistics_method' => $validated['logistics_method'],
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
            return back()->with('error', 'Chỉ có thể chuyển sang Shipping Back sau khi yêu cầu đã được duyệt.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        $this->transition(
            returnRequest: $returnRequest,
            toStatus: ReturnRequest::STATUS_SHIPPING_BACK,
            note: 'Đang vận chuyển hàng trả về kho.',
            extraAttributes: [
                'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
                'shipping_back_at' => now(),
            ],
        );

        return back()->with('success', 'Đã chuyển yêu cầu sang Shipping Back.');
    }

    public function receive(Request $request, ReturnRequest $returnRequest)
    {
        if (! $returnRequest->canBeMarkedReceived()) {
            return back()->with('error', 'Chỉ có thể đánh dấu Received sau bước Shipping Back.');
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
            return back()->with('error', 'Chỉ có thể chuyển sang Inspecting sau khi hàng đã được nhận.');
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
            note: 'Đã tạo xử lý đổi hàng cho khách.',
            extraAttributes: [
                'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
                'resolved_at' => now(),
            ],
        );

        return back()->with('success', 'Đã chuyển yêu cầu sang trạng thái đổi hàng.');
    }

    public function complete(Request $request, ReturnRequest $returnRequest)
    {
        if (! $returnRequest->canBeCompleted()) {
            return back()->with('error', 'Chỉ có thể hoàn tất sau khi đã hoàn tiền hoặc đổi hàng.');
        }

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        $this->transition(
            returnRequest: $returnRequest,
            toStatus: ReturnRequest::STATUS_COMPLETED,
            note: 'Yêu cầu trả hàng đã hoàn tất.',
            extraAttributes: [
                'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
                'completed_at' => now(),
            ],
        );

        return back()->with('success', 'Đã hoàn tất yêu cầu trả hàng.');
    }

    private function loadDetails(ReturnRequest $returnRequest): void
    {
        $returnRequest->load([
            'user',
            'order.user',
            'orderItem.product',
            'statusHistories.changedBy',
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
}
