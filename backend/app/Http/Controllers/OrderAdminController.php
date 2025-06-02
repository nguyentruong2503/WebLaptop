<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderAdmin;

class OrderAdminController extends Controller
{
    // API: Lấy danh sách đơn hàng cho admin
    public function index(Request $request)
    {
        // Có thể thêm filter theo trạng thái, tìm kiếm, v.v. ở đây
        $query = OrderAdmin::query();
        if ($request->has('status') && $request->status !== 'ALL') {
            $query->where('orderstatus', $request->status); // Sửa lại đúng tên cột
        }
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fullName', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('id', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%") ;
            });
        }
        $orders = $query->orderByDesc('created_at')->get();
        return response()->json($orders);
    }

    // API: Cập nhật trạng thái đơn hàng (xác nhận/hủy)
    public function updateStatus(Request $request, $id)
    {
        $order = OrderAdmin::findOrFail($id);
        $status = $request->input('status');
        if (!in_array($status, ['Confirmed', 'Cancelled'])) {
            return response()->json(['error' => 'Trạng thái không hợp lệ'], 400);
        }
        $order->orderstatus = $status;
        $order->save();
        return response()->json(['success' => true, 'order' => $order]);
    }
}
