<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class CartController extends Controller
{
    //
    public function addToCart(Request $request)
    { 
        $user=request()->user();
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
           
            'idProduct' => 'required',
            'quantity' => 'required|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $idUser =  $user->id;
        $idProduct = $request->idProduct;
        $quantity = $request->quantity;

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $cartItem = Cart::where('userID', $idUser)
            ->where('productID', $idProduct)
            ->first();

        if ($cartItem) {
            // Cập nhật số lượng
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Thêm mới
            Cart::create([
                'userID' => $idUser,
                'productID' => $idProduct,
                'quantity' => $quantity,
            ]);
        }

        return response()->json([
            'message' => 'Thêm vào giỏ hàng thành công',
        ], 200);
    }
    public function getCartByUser(Request $request)
    {
        $user=request()->user();
        $cartItems = Cart::with('product')
            ->where('userID', $user->id)
            ->get()->map(function ($item) {
                return [
                    'id'        => $item->id,
                    'product_id' => $item->product->id,
                    'name'      => $item->product->productName,
                    'image'     => $item->product->img ?? null,
                    'price'     => $item->product->price,
                    'quantity'  => $item->quantity,
                    'total'     => $item->quantity * $item->product->price,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $cartItems
        ]);
    }
    public function updateCart(Request $request, $cartId)
    {
        $cart = Cart::findOrFail($cartId);
        $cart->quantity = $request->input('quantity', 1);
        $cart->save();

        return response()->json(['status' => 'success']);
    }
    public function deleteCart($cartId)
    {
        Cart::destroy($cartId);

        return response()->json(['status' => 'deleted']);
    }
}
