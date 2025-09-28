<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Order_detail;
use Illuminate\Http\Request;

class Payment_OrderController extends Controller
{
    //

    public function store(Request $request)
    {
        $user=request()->user();
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        // ✅ Truy vấn cart từ DB
        $cartItems = Cart::where('userID',  $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Giỏ hàng trống'], 400);
        }

        // ✅ Tính tổng tiền
        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });


        // ✅ Tạo đơn hàng
        $order = Order::create([
            'userID' => $user->id,
            'totalAmount' => $total,
            'fullName' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        foreach ($cartItems as $item) {
            Order_detail::create([
                'orderID' => $order->id,
                'productID' => $item->productID,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
            ]);
        }
        Cart::where('userID', $user->id)->delete();

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }
}
