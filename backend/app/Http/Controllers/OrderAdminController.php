<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderAdmin;
use App\Models\Order_detail; // Sửa lại đúng tên model
use App\Models\Product;
use App\Models\Order; // Thêm import cho model Order
use Carbon\Carbon;


class OrderAdminController extends Controller
{
    // API: Lấy danh sách đơn hàng cho admin
    public function index(Request $request)
    {
        $query = OrderAdmin::query();
        
        if ($request->has('status') && $request->status !== 'ALL') {
            // Map frontend status to database enum values
            $statusMap = [
                'Pending' => 'Pending',
                'Confirmed' => 'Confirmed', 
                'Shipped' => 'Shipped',
                'Delivered' => 'Delivered',
                'Cancelled' => 'Cancelled'
            ];
            
            $dbStatus = $statusMap[$request->status] ?? $request->status;
            $query->where('orderstatus', $dbStatus);
        }
        

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fullName', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('id', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%");
            });
        }
        // Lọc theo mốc thời gian
        if ($request->has('time') && $request->time && $request->time !== 'ALL') {
            $now = Carbon::now();
            if ($request->time === 'today') {
                $query->whereDate('created_at', $now->toDateString());
            } elseif ($request->time === 'last_7_days') {
                $query->whereDate('created_at', '>=', $now->copy()->subDays(6)->toDateString());
            } elseif ($request->time === 'last_30_days') {
                $query->whereDate('created_at', '>=', $now->copy()->subDays(29)->toDateString());
            }
        }
        $orders = $query->orderByDesc('created_at')->get();
        
        // Thêm trường status cho từng order để đồng bộ với frontend
        foreach ($orders as $order) {
            $order->status = $order->orderstatus;
        }
        

        return response()->json($orders);
    }

    // API: Cập nhật trạng thái đơn hàng (xác nhận/hủy)
    public function updateStatus(Request $request, $id)
    {

        try {
            $order = OrderAdmin::find($id);
            
            if (!$order) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Không tìm thấy đơn hàng'
                ], 404);
            }
            
            $status = strtolower($request->input('status'));
            
            // Map frontend status to correct enum values
            $enumMap = [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'shipped' => 'Shipped', 
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled',
            ];
            
            if (!isset($enumMap[$status])) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Trạng thái không hợp lệ: ' . $status
                ], 400);
            }
            
            $enumStatus = $enumMap[$status];
            $order->orderstatus = $enumStatus;
            $order->save();

            // Nếu xác nhận đơn hàng, trừ số lượng sản phẩm
            if ($enumStatus === 'Confirmed') {
                // Lấy chi tiết đơn hàng từ bảng Order_detail
                $orderDetails = Order_detail::where('orderID', $id)->get(); // Sử dụng đúng model
                foreach ($orderDetails as $detail) {
                    $product = Product::find($detail->productID);
                    if ($product) {
                        $product->quality = max(0, $product->quality - $detail->quantity);
                        $product->save();
                    }
                }
            }

            // Refresh the model to get updated data
            $order->refresh();
            $order->status = $order->orderstatus;
            
            // Đồng bộ sang bảng orders nếu có model khác
            $orderModel = Order::find($id);
            if ($orderModel) {
                $orderModel->orderstatus = $enumStatus;
                $orderModel->save();
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Cập nhật trạng thái thành công',
                'order' => $order
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
}