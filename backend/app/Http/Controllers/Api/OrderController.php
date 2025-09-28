<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{

    //Lấy đơn theo theo userId 
    public function getOrderByUser(Request $request)
    {
         

             $user=request()->user();

        $userId = $user->id;

        $keyword = $request->query('keyword'); 
        $status = $request->query('status');   

        $query = Order::with('details.product') //Join bảng
            ->where('userID', $userId);         

        if ($status) {
            $query->where('orderstatus', $status);
        }

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('id', 'like', "%$keyword%") 
                ->orWhereHas('details.product', function ($q2) use ($keyword) {
                    $q2->where('productName', 'like', "%$keyword%"); 
                });
            });
        }

        $orders = $query->get(); 
        return response()->json($orders);
    }
    
    public function getOrderDetailByOrderId($id)
    {
        $order = Order::with('details.product')->find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json($order);
    }

    //Người dùng hủy đơn
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'orderstatus' => 'required|string|in:Pending,Confirmed,Shipped,Delivered,Cancelled',
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->orderstatus = $request->orderstatus;
        $order->save();

        return response()->json(['message' => 'Order status updated', 'order' => $order]);
    }
}
