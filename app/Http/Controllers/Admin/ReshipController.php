<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reship;
use Illuminate\Http\Request;

/**
 * ReshipController
 *
 * Quản lý việc gửi lại hàng cho khách:
 *   - Nhập mã vận đơn (tracking_code) và đơn vị vận chuyển (carrier)
 *   - Cập nhật trạng thái: pending → shipped → delivered
 */
class ReshipController extends Controller
{
    /**
     * Danh sách các lệnh reship đang chờ xử lý
     */
    public function index()
    {
        $reships = Reship::with(['returnRequest.user', 'returnRequest.order'])
            ->latest()
            ->paginate(15);

        return view('admin.reships.index', compact('reships'));
    }

    /**
     * Cập nhật thông tin vận chuyển (tracking_code, carrier) và trạng thái
     */
    public function update(Request $request, Reship $reship)
    {
        $validated = $request->validate([
            'tracking_code' => ['nullable', 'string', 'max:100'],
            'carrier'       => ['nullable', 'string', 'max:100'],
            'status'        => ['required', 'in:pending,shipped,delivered'],
        ]);

        $reship->update($validated);

        return back()->with('success', 'Đã cập nhật thông tin vận chuyển.');
    }
}
