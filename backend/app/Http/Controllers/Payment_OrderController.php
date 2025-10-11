<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Order_detail;
use Illuminate\Http\Request;

class Payment_OrderController extends Controller
{
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
            'payment_method' => 'COD',
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

    public function cod(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:carts,id',
        ]);

        $productIds = $request->product_ids;

        $cartItems = Cart::where('userID', $user->id)
            ->whereIn('id', $productIds)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Giỏ hàng trống hoặc sản phẩm đã được xử lý'], 400);
        }

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        $order = Order::create([
            'userID' => $user->id,
            'totalAmount' => $total,
            'fullName' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'orderStatus' => 'Pending',
            'payment_method' => 'COD',
        ]);

        foreach ($cartItems as $item) {
            Order_detail::create([
                'orderID' => $order->id,
                'productID' => $item->productID,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
            ]);
        }

        Cart::where('userID', $user->id)
            ->whereIn('id', $productIds)
            ->delete();

        // ✅ Trả về JSON có URL redirect
        return response()->json([
            'success' => true,
            'redirect_url' => 'http://127.0.0.1:5501/frontend/client/payment_success.html'
                . '?order_id=' . $order->id
                . '&amount=' . $total
                . '&time=' . urlencode($order->created_at->format('Y-m-d H:i:s'))
                . '&message=Thanh toán khi nhận hàng'
                . '&email=' . urlencode($user->email)
        ]);
    }



    public function vnpay(Request $request)
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://127.0.0.1:8000/api/payment/vnpay-return";
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        $vnp_TxnRef = $request->input('order_id');
        $vnp_OrderInfo = json_encode([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->user()->email,
            'product_ids' => $request->product_ids,
        ], JSON_UNESCAPED_UNICODE);

        $vnp_OrderType = "billpayment";
        $vnp_Amount = $request->input('amount') * 100;
        $vnp_Locale = "vn";
        $vnp_BankCode = $request->input('bank_code', 'NCB');
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        return response()->json([
            'code' => '00',
            'message' => 'success',
            'payment_url' => $vnp_Url,
        ]);
    }

    public function vnpayReturn(Request $request)
    {
        // Nhận toàn bộ dữ liệu từ VNPay callback
        $inputData = $request->all();

        $orderInfo = json_decode($request->vnp_OrderInfo, true);


        if (!is_array($orderInfo) || !isset($orderInfo['user_id'])) {
            return response()->json(['error' => 'Dữ liệu trả về không hợp lệ'], 400);
        }

        $userId = $orderInfo['user_id'];
        $user = \App\Models\User::find($userId);

        if ($request->vnp_ResponseCode === '00') {
            $productIds = $orderInfo['product_ids'] ?? [];

            $cartItems = Cart::where('userID', $userId)
                ->whereIn('id', $productIds) 
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'Giỏ hàng trống hoặc đã xử lý'], 400);
            }

            $total = $cartItems->sum(fn($i) => $i->product->price * $i->quantity);

            $order = Order::create([
                'userID' => $userId,
                'totalAmount' => $total,
                'fullName' => $orderInfo['name'] ?? 'Không rõ',
                'phone' => $orderInfo['phone'] ?? '',
                'address' => $orderInfo['address'] ?? '',
                'orderStatus' => 'Pending',
                'payment_method' => 'VNPay',
                'bankCode' => $request->vnp_BankCode ?? null,
                'cardType' => $request->vnp_CardType ?? null,
            ]);

            foreach ($cartItems as $item) {
                Order_detail::create([
                    'orderID' => $order->id,
                    'productID' => $item->productID,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ]);
            }

            Cart::where('userID', $userId)
                ->whereIn('id', $productIds)
                ->delete();


            //Thanh toán thành công
            return redirect()->away('http://127.0.0.1:5501/frontend/client/payment_success.html'
                . '?order_id=' . $order->id
                . '&amount=' . $total
                . '&time=' . urlencode($order->created_at->format('Y-m-d H:i:s'))
                . '&message=VNPay'
                . '&email=' . urlencode($user->email)
            );
                                    
        }

        return view('payment_fail');
    }



}
