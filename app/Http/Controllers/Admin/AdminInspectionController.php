<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\Refund;
use App\Models\Reship;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AdminInspectionController
 *
 * Xử lý bước KIỂM TRA HÀNG (INSPECTION) - bước bắt buộc trong quy trình.
 *
 * Luồng xử lý:
 *   1. Admin nhận hàng → tạo Inspection (is_valid = true/false)
 *   2. Nếu is_valid = false (gian lận):
 *      → return_request.status = rejected
 *      → users.violation_count + 1
 *      → tạo Reship (gửi trả hàng lại cho khách)
 *   3. Nếu is_valid = true (hợp lệ):
 *      → Nếu type = refund → tạo Refund + status = approved
 *      → Nếu type = exchange → tạo Reship + status = approved
 */
class AdminInspectionController extends Controller
{
    /**
     * Hiển thị danh sách yêu cầu đang chờ kiểm tra
     */
    public function index()
    {
        // Lấy các yêu cầu đang ở trạng thái inspecting (đang kiểm tra)
        // hoặc pending (chờ xử lý) để admin xem và tạo inspection
        $returnRequests = ReturnRequest::with(['user', 'order', 'inspection'])
            ->whereIn('status', [
                ReturnRequest::STATUS_PENDING,
                ReturnRequest::STATUS_INSPECTING,
            ])
            ->latest()
            ->paginate(15);

        return view('admin.inspections.index', compact('returnRequests'));
    }

    /**
     * Hiển thị form kiểm tra hàng cho 1 yêu cầu cụ thể
     */
    public function show(ReturnRequest $returnRequest)
    {
        $returnRequest->load(['user', 'order', 'orderItem', 'inspection', 'reship', 'refund']);

        return view('admin.inspections.show', compact('returnRequest'));
    }

    /**
     * Xử lý kết quả kiểm tra hàng
     *
     * Đây là action quan trọng nhất - thực hiện toàn bộ logic nghiệp vụ
     */
    public function process(Request $request, ReturnRequest $returnRequest)
    {
        // Kiểm tra xem đã có inspection chưa (tránh xử lý 2 lần)
        if ($returnRequest->inspection) {
            return back()->with('error', 'Yêu cầu này đã được kiểm tra rồi.');
        }

        $validated = $request->validate([
            'is_valid' => ['required', 'boolean'],
            'note'     => ['nullable', 'string', 'max:1000'],
            // Chỉ cần khi hàng hợp lệ + type = refund
            'amount'   => ['required_if:is_valid,1', 'nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($returnRequest, $validated) {

            // Bước 1: Tạo bản ghi Inspection (lưu kết quả kiểm tra)
            Inspection::create([
                'return_id' => $returnRequest->id,
                'is_valid'  => $validated['is_valid'],
                'note'      => $validated['note'] ?? null,
            ]);

            // Bước 2: Xử lý theo kết quả kiểm tra
            if (! $validated['is_valid']) {
                // ===== CASE 1: HÀNG KHÔNG HỢP LỆ (GIAN LẬN) =====

                // Cập nhật trạng thái yêu cầu → rejected
                $returnRequest->update(['status' => ReturnRequest::STATUS_REJECTED]);

                // Tăng violation_count của khách hàng lên 1
                $returnRequest->user->increment('violation_count');

                // Tạo Reship để gửi trả hàng lại cho khách
                Reship::create([
                    'return_id' => $returnRequest->id,
                    'status'    => 'pending',
                    // tracking_code và carrier sẽ được nhập sau
                ]);

            } else {
                // ===== CASE 2: HÀNG HỢP LỆ =====

                // Cập nhật trạng thái yêu cầu → approved
                $returnRequest->update(['status' => ReturnRequest::STATUS_APPROVED]);

                if ($returnRequest->request_type === ReturnRequest::TYPE_REFUND) {
                    // Loại REFUND → tạo Refund (hoàn tiền)
                    Refund::create([
                        'return_id' => $returnRequest->id,
                        'amount'    => $validated['amount'],
                        'status'    => 'pending',
                    ]);

                } else {
                    // Loại EXCHANGE → tạo Reship (gửi hàng đổi)
                    Reship::create([
                        'return_id' => $returnRequest->id,
                        'status'    => 'pending',
                    ]);
                }
            }
        });

        $message = $validated['is_valid']
            ? 'Kiểm tra hoàn tất. Đã xử lý ' . ($returnRequest->request_type === 'refund' ? 'hoàn tiền' : 'đổi hàng') . '.'
            : 'Phát hiện gian lận. Đã từ chối và tạo lệnh gửi trả hàng.';

        return redirect()
            ->route('admin.inspections.show', $returnRequest)
            ->with('success', $message);
    }
}
